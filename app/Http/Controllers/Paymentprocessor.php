<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courses;
use App\Models\Enrollments;
use App\Models\Transactions;
use App\Models\Intrestform;
use App\Models\User;
use Validator;
use Carbon\Carbon;
use App\Mail\Receiptmail;
use App\Mail\Adminconfirmpayments;
use Illuminate\Support\Facades\Mail;
class Paymentprocessor extends Controller
{   
    //pay with paysatck
    public function initialize_payments($email,$amount,$link){
        $url = "https://api.paystack.co/transaction/initialize";
        $fields = [
          'email' => $email,
          'amount' => $amount,
          'callback_url'=>$link,
        ];
        $fields_string = http_build_query($fields);
        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Authorization: Bearer sk_test_78d3222355597d8e13ada75b3f02230f6849d4d8",
          "Cache-Control: no-cache",
        ));
        
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        
        //execute post
        $result = curl_exec($ch);
        return $result;
    }
//verify paystack transactions
    public function verify_payments($reference){
    
     
     $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/{$reference}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer sk_test_78d3222355597d8e13ada75b3f02230f6849d4d8",
      "Cache-Control: no-cache",
    ),
  ));
  
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);                   
  return $response;
  
    }
    public function verify_pluralcodepayment(Request $request){
        $carbondate=Carbon::now();
        $time=$carbondate->now('UTC')->setTimezone('WAT')->format('g:i:s a');
        $date=$carbondate->toDateString();
        $reference=$request->reference;
        $first_name=$request->first_name;
        $last_name=$request->last_name;
        $name= $first_name.' '.$last_name;
        $phone_number=$request->phone_number;
        $type=$request->type;
        $amount=$request->amount;
        $course=$request->course;
        $email=$request->email;
        $paymentmode=$request->paymentmode;
        $learningmode=$request->learning_mode;
        if($reference ){
            if($type=="enrollment"){
                $response= $this->verify_payments($reference);
                $verify_payment_response= json_decode($response);
                if($verify_payment_response->status == true){
               
                                       $enrollment=Enrollments::create([
                                           'name'=> $name,
                                           'email'=>$email,
                                           'mode_of_learning'=>$learningmode,
                                           'course_of_interest'=>$course,
                                           'mode_of_payment'=>$paymentmode,
                                           'payment_status'=>"complete",
                                           'date'=>$date,
                                           'time'=>$time
                                       ]); 
                                       
                                       Transactions::create([
                                           'students_id'=>$enrollment->id,
                                           'amount_paid'=>$amount,
                                           'mode_of_payment'=>$paymentmode,
                                           'date'=>$date,
                                           'time'=>$time
                                       ]);
                                       $admin_email=["email"=>$admin->email];
                                       $title= $course.' '.'Receipt';
                                       $admin_title=$name.' '.'payment';
                                       $student_info=[
                                         "name"=>$name,
                                         "email"=>$email
                                       ];
                       
                                       $transaction_details=[
                                       "type"=>"enrollment",
                                       "mode_of_payment"=>"card",
                                       "course_name"=> $course,
                                       "Amount_paid"=> $amount
                                       ];
                              
                                   
                                   
                                  $sendmail= Mail::to( $admin_email['email'])->send(new Adminconfirmpayments($admin_title,$student_info,$transaction_details));
                                  $send_adminemail= Mail::to( $student_info['email'])->send(new Receiptmail($title,$student_info,$transaction_details));
                                  if(empty($sendmail)){
                                   return response()->json([
                                       "status"=>"success"
                                      ]);
                                  }
           
                                        
                        }
            }if($type=="interestform"){
                $response= $this->verify_payments($reference);
                $verify_payment_response= json_decode($response);
                if($verify_payment_response->status == true){
                    $admin=User::first();
                                       $enrollment=Intrestform::create([
                                           'name'=> $name,
                                           'email'=>$email,
                                           'mode_of_learning'=>$learningmode,
                                           'course_of_interest'=>$course,
                                           'mode_of_payment'=>$paymentmode,
                                           'payment_status'=>"complete",
                                           'phone_number'=>$phone_number,
                                           'amount_paid'=>$amount,
                                           'date'=>$date,
                                           'time'=>$time
                                       ]); 
                                       
                                       $admin_email=["email"=>$admin->email];
                                       $title= $course.' '.'Receipt';
                                       $admin_title=$name.' '.'payment';
                                       $student_info=[
                                         "name"=>$name,
                                         "email"=>$email
                                       ];
                       
                                       $transaction_details=[
                                       "type"=>"interestform",
                                       "mode_of_payment"=>"card",
                                       "course_name"=> $course,
                                       "Amount_paid"=> $amount
                                       ];
                              
                                   
                                   
                                  $sendmail= Mail::to( $admin_email['email'])->send(new Adminconfirmpayments($admin_title,$student_info,$transaction_details));
                                  $send_adminemail= Mail::to( $student_info['email'])->send(new Receiptmail($title,$student_info,$transaction_details));
                                  if(empty($sendmail)){
                                   return response()->json([
                                       "status"=>"success"
                                      ]);
                                  }
                                      
           
                                        
                    }
            }if($type=="payment_form"){
                $response= $this->verify_payments($reference);
                $verify_payment_response= json_decode($response);
                if($verify_payment_response->status == true){
                $enrollment=Enrollments::where('email',$email)->first();
                $name=$enrollment->name;
                $admin=User::first();
                

                Transactions::create([
                    'students_id'=>$enrollment->id,
                    'amount_paid'=>$amount,
                    'mode_of_payment'=>$paymentmode,
                    'date'=>$date,
                    'time'=>$time
                ]);
                $admin_email=["email"=>$admin->email];
                $title= $course.' '.'Receipt';
                $admin_title=$name.' '.'payment';
                $student_info=[
                  "name"=>$name,
                  "email"=>$email
                ];

                $transaction_details=[
                "type"=>"payment_form",
                "mode_of_payment"=>"card",
                "course_name"=> $course,
                "Amount_paid"=> $amount
                ];
       
            
            
           $sendmail= Mail::to( $admin_email['email'])->send(new Adminconfirmpayments($admin_title,$student_info,$transaction_details));
           $send_adminemail= Mail::to( $student_info['email'])->send(new Receiptmail($title,$student_info,$transaction_details));
           if(empty($sendmail)){
            return response()->json([
                "status"=>"success"
               ]);
           }
        }
                
            }
         
        }else{
            if($type=="enrollment"){
                $link="https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course}&amount={$amount}&type=enrollment&paymentmode=Transfer&learning_mode={$learningmode}";
                //code for sending email to admin to verify
                $admin=User::first();
                $admin_email=["email"=>$admin->email];
                
                $admin_title=$name.' '.'payment';
                $student_info=[
                  "name"=>$name,
                  "email"=>$email
                ];

                $transaction_details=[
                "type"=>"enrollment",
                "mode_of_payment"=>"transfer",
                "course_name"=> $course,
                "Amount_paid"=> $amount,
                "link"=>$link
                ];
       
            
            
           $sendmail= Mail::to( $admin_email['email'])->send(new Adminconfirmpayments($admin_title,$student_info,$transaction_details));
           
           if(empty($sendmail)){
            return response()->json([
                "status"=>"success"
               ]);
           }
            }if($type=="interestform"){
                $link="https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course}&amount={$amount}&phone_number={$phone_number}&type=interestform&paymentmode=Transfer&learning_mode={$learningmode}";
                $admin=User::first();
                $admin_email=["email"=>$admin->email];
                
                $admin_title=$name.' '.'payment';
                $student_info=[
                  "name"=>$name,
                  "email"=>$email
                ];

                $transaction_details=[
                "type"=>"interestform",
                "mode_of_payment"=>"transfer",
                "course_name"=> $course,
                "Amount_paid"=> $amount,
                "link"=>$link
                ];
       
            
            
           $sendmail= Mail::to( $admin_email['email'])->send(new Adminconfirmpayments($admin_title,$student_info,$transaction_details));
           
           if(empty($sendmail)){
            return response()->json([
                "status"=>"success"
               ]);
           }
            }
            if($type=="payment_form"){
                $link="https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&amount={$amount}&type=payment_form&paymentmode=Transfer";
                $admin=User::first();
                $admin_email=["email"=>$admin->email];
                
                $admin_title=$name.' '.'payment';
                $student_info=[
                  "name"=>$name,
                  "email"=>$email
                ];

                $transaction_details=[
                "type"=>"payment_form",
                "mode_of_payment"=>"transfer",
                "course_name"=> $course,
                "Amount_paid"=> $amount,
                "link"=>$link
                ];
       
            
            
           $sendmail= Mail::to( $admin_email['email'])->send(new Adminconfirmpayments($admin_title,$student_info,$transaction_details));
           
           if(empty($sendmail)){
            return response()->json([
                "status"=>"success"
               ]);
           }
            }
           
        }

      
    }
    //process payments
    public function payment_processsor(Request $request){
        
        $request_type= $request->type;
        if($request_type == "enrollment"){
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name'=> 'required',
                'email'=>'required',
                'mode_of_learning' => 'required',
                'course_of_interest'=>'required',
                'mode_of_payment' => 'required',
               
                
            ]);
    
            if($validator->fails()){
                return response()->json(['error' => $validator->errors()->all()]);
            }else{
             //check payment mode

             $first_name=$request->first_name;
             $last_name=$request->last_name;
             $email=$request->email;
             $course_of_intrest=$request->course_of_interest;
             $paymentmode=$request->mode_of_payment;
             $get_course= Courses::where('name',$course_of_intrest)->first();
             $course_fee=$get_course->course_fee;
             $amount_inputed=$request->inputed_amount_to_pay;
             
             $learningmode=$request->mode_of_learning;
             if($paymentmode == "Card"){
                $cal_amount=(int)$course_fee * 100;
                $amount= $cal_amount;
                  if(isset($amount_inputed)){
                      $amount_to_pay=(int)$amount_inputed * 100;
                      $link="https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course_of_intrest}&amount={$amount_inputed}&type=enrollment&paymentmode=Card&learning_mode={$learningmode}";
                     //initialize and verify payments
                $payment=$this->initialize_payments($email,$amount_to_pay,$link);
                $paystack_response=json_decode($payment);
                if(isset($paystack_response->status) == true){
                  
                    
                        
                       
                        return response()->json([
                            "payment_mode"=>"card",
                            "payment_link"=> $paystack_response->data->authorization_url,
                            "course_fee"=>$course_fee,
                            "amount_to_pay"=>$amount_inputed,
                            "course_name"=>$request->course_of_interest
                          ]);
                     
                        
                  
                }else{
                    return response()->json([
                        "status"=>"failed",
                        "message"=>"can not verify payments, an error occured"
                    ]);
                }
                  }else{
                      
                      //initialize and verify payments
                $link="https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course_of_intrest}&amount={$course_fee}&type=enrollment&paymentmode=Card&learning_mode={$learningmode}";
                $payment=$this->initialize_payments($email,$amount,$link);
                $paystack_response=json_decode($payment);
                if(isset($paystack_response->status) == true){
                   
                    
    
                            return response()->json([
                                "payment_mode"=>"card",
                                "payment_link"=> $paystack_response->data->authorization_url,
                                "course_fee"=>$course_fee,
                                "amount_to_pay"=>$course_fee,
                                "course_name"=>$request->course_of_interest
                              ]);
                        
                    
                  
                }else{
                    return response()->json([
                        "status"=>"failed",
                        "message"=>"can not verify payments, an error occured"
                    ]);
                }
                  }
                
                //process bank transfer
             }elseif($paymentmode == "Bank Transfer"){
                if(isset($amount_inputed)){
                    $admin_info= User::first();
                    return response()->json([
                        "status"=>"success",
                        "payment_mode"=> "Bank Transfer",
                        "course_fee"=>$course_fee,
                        "amount_to_pay"=>$amount_inputed,
                        "course_name"=>$request->course_of_interest,
                        "bank_name"=>$admin_info->bank_name,
                        "bank_account_number"=>$admin_info->bank_account_number,
                        "bank_account_name"=>$admin_info->bank_account_name,
                        "payment_link"=>"https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course_of_intrest}&amount={$amount_inputed}&type=enrollment&paymentmode=Transfer&learning_mode={$learningmode}"
                      ]);
                    
                }else{ 
                    $admin_info= User::first();
                    return response()->json([
                        "status"=>"success",
                        "payment_mode"=> "Bank Transfer",
                        "course_fee"=>$course_fee,
                        "amount_to_pay"=>$course_fee,
                        "course_name"=>$request->course_of_interest,
                        "bank_name"=>$admin_info->bank_name,
                        "bank_account_number"=>$admin_info->bank_account_number,
                        "bank_account_name"=>$admin_info->bank_account_name,
                        "payment_link"=>"https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course_of_intrest}&amount={$course_fee}&type=enrollment&paymentmode=Transfer&learning_mode={$learningmode}"
                      ]);
                }
                
             }else{
                 return response()->json([
                    "status"=>"failed",
                    "message"=>"can not process payment"
                 ]);
             }
               
              
            }
        }
        if($request_type == "intrestform"){
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name'=>'required',
                'email'=>'required',
                'mode_of_learning' => 'required',
                'course_of_interest'=>'required',
                'phone_number'=>'required',
               
            ]);
            if($validator->fails()){
                return response()->json(['error' => $validator->errors()->all()]);
            }else{

              //check payment mode
              $first_name=$request->first_name;
              $last_name=$request->last_name;
              $email=$request->email;
              $paymentmode=$request->mode_of_payment;
              $phone_number=$request->phone_number;
              $intrest_form_fee= 10000;
              $cal_amount= $intrest_form_fee * 100;
              $amount= $cal_amount;
              $course_of_intrest=$request->course_of_interest;
              $get_course= Courses::where('name',$course_of_intrest)->first();
              $course_fee=$get_course->course_fee;
              $learningmode=$request->mode_of_learning;
             
             if($paymentmode == "Card"){
                $link="https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course_of_intrest}&phone_number={$phone_number}&amount={$intrest_form_fee}&type=interestform&paymentmode=Card&learning_mode={$learningmode}";
                     //initialize and verify payments
                $payment=$this->initialize_payments($email,$amount,$link);
                $paystack_response=json_decode($payment);
                if(isset($paystack_response->status) == true){
                   
                    
                        $reference=$paystack_response->data->reference;
                      

                            return response()->json([
                                "status"=>"success",
                                "payment_mode"=>"card",
                                "payment_link"=> $paystack_response->data->authorization_url,
                                "course_fee"=>$course_fee,
                                "amount_to_pay"=>$intrest_form_fee,
                                "course_name"=>$request->course_of_interest
                              ]);

                        
                    
                    
                } 
             }elseif($paymentmode == "Bank Transfer"){
                
                $admin_info = User::first();
                return response()->json([
                    "status"=>"success",
                    "payment_mode"=> "Bank Transfer",
                    "course_fee"=>$course_fee,
                    "amount_to_pay"=>$intrest_form_fee,
                    "course_name"=>$request->course_of_interest,
                    "bank_name"=>$admin_info->bank_name,
                    "bank_account_number"=>$admin_info->bank_account_number,
                    "bank_account_name"=>$admin_info->bank_account_name,
                    "payment_link"=>"https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course_of_intrest}&phone_number={$phone_number}&amount={$intrest_form_fee}&type=interestform&paymentmode=Card&learning_mode={$learningmode}"
                  ]);
             }else{

                $intrestform=Intrestform::create([
                    'name' => $name,
                    'email'=>$request->email,
                    'mode_of_learning' => $request->mode_of_learning,
                    'course_of_interest'=>$request->course_of_interest,
                    'payment_status'=>"pendding",
                    'phone_number'=>$request->phone_number,
                    'date'=>$date,
                    'time'=>$time,
                ]); 
                return response()->json([
                    "status"=>"success",
                    "payment_mode"=>"NULL",
                    "message"=>"message saved"
                  ]);
                }
            }
        }
        

        if($request_type == "payment_form"){
            $validator = Validator::make($request->all(), [
                'mode_of_payment' => 'required',
                'email'=>'required',
                'course_to_payfor'=>'required',
                
               
            ]);
            if($validator->fails()){
                return response()->json(['error' => $validator->errors()->all()]);
            }else{
               //check if student exist in database
               $email=$request->email;
               $course=$request->course_to_payfor;
               $check_student= Enrollments::where('email',$email)->where('course_of_interest',$course)->first();
               if($check_student){
                $amount_inputed=(int)$request->inputed_amount_to_pay;
                $paymentmode=$request->mode_of_payment;
                $cal_amount=  $amount_inputed * 100;
                $amount= $cal_amount;
                $get_course= Courses::where('name',$course)->first();
                $course_fee=$get_course->course_fee;
               if($paymentmode == "Card"){
                   if(isset($amount_inputed)){
                       $link="https://pluralcode.academy/payment_verification?email={$email}&course={$course}&amount={$amount_inputed}&course={$course}&type=payment_form&paymentmode=Card";
                       $payment=$this->initialize_payments($email,$amount,$link);
                       $paystack_response=json_decode($payment);
                       if($paystack_response->status == true){
                           
                           
                                   return response()->json([
                                     "status"=>"success",
                                     "payment_mode"=>"card",
                                     "payment_link"=> $paystack_response->data->authorization_url,
                                     "amount_to_pay"=>$amount_inputed,
                                     "course_name"=>$request->course_to_payfor
                                   ]);
                              
                           
                           
                       }
                   }else{
                    $course_amount= (int)$course_fee * 100;
                    $link="https://pluralcode.academy/payment_verification?email={$email}&course={$course}&amount={$course_fee}&course={$course}&type=payment_form&paymentmode=Card";
                    $payment=$this->initialize_payments($email,$course_amount,$link);
                    $paystack_response=json_decode($payment);
                    if($paystack_response->status == true){
                        
                                return response()->json([
                                  "status"=>"success",
                                  "payment_mode"=>"card",
                                  "payment_link"=> $paystack_response->data->authorization_url,
                                  "amount_to_pay"=>$course_fee,
                                  "course_name"=>$request->course_to_payfor
                                ]);
                           
                        
                        
                    }
                   }
                  //initialize and verify payments
                 
               }elseif($paymentmode == "Bank Transfer"){
                 
                  $admin_info= User::first();
                  if(isset($amount_inputed)){
                    return response()->json([
                        "status"=>"success",
                        "payment_mode"=> "Bank Transfer",
                        "amount_to_pay"=>$amount_inputed,
                        "course_name"=>$request->course_to_payfor,
                        "bank_name"=>$admin_info->bank_name,
                        "bank_account_number"=>$admin_info->bank_account_number,
                        "bank_account_name"=>$admin_info->bank_account_name,
                        "payment_link"=>"https://pluralcode.academy/payment_verification?email={$email}&course={$course}&amount={$amount_inputed}&course={$course}&type=payment_form&paymentmode=Transfer"
                      ]);
                  }else{
                    return response()->json([
                        "status"=>"success",
                        "payment_mode"=> "Bank Transfer",
                        "amount_to_pay"=>$amount_inputed,
                        "course_name"=>$request->course_to_payfor,
                        "bank_name"=>$admin_info->bank_name,
                        "bank_account_number"=>$admin_info->bank_account_number,
                        "bank_account_name"=>$admin_info->bank_account_name,
                        "payment_link"=>"https://pluralcode.academy/payment_verification?email={$email}&course={$course}&amount={$course_fee}&course={$course}&type=payment_form&paymentmode=Transfer"
                      ]);
                  }
                  
               }else{
                  return response()->json([
                     "status"=>"failed",
                     "message"=>"can not process payment"
                  ]);
              }
               }else{
                   return response()->json([
                       "status"=>"failed",
                       "message"=>"Email not registered"
                   ]);
               } 
             
            }
        }
      
    }
}
