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
    public function verify_payments(Request $request){
     $reference=$request->reference;
     $name=$request->name;
     $email=
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
  $verify_payment_response= json_decode($response);
     if($verify_payment_response->status == true){
    
                            
                            $enrollment=Enrollments::create([
                                'name'=> $name,
                                'email'=>$request->email,
                                'mode_of_learning'=>$request->mode_of_learning,
                                'course_of_interest'=>$request->course_of_interest,
                                'mode_of_payment'=>$request->mode_of_payment,
                                'payment_status'=>"complete",
                                'date'=>$date,
                                'time'=>$time
                            ]); 
                            
                            Transactions::create([
                                'students_id'=>$enrollment->id,
                                'amount_paid'=>$amount_inputed,
                                'mode_of_payment'=>$request->mode_of_payment,
                                'date'=>$date,
                                'time'=>$time
                            ]);
                            return response()->json([
                                "payment_link"=> $paystack_response->data->authorization_url,
                                "course_fee"=>$course_fee,
                                "amount_to_pay"=>$amount_inputed,
                                "course_name"=>$request->course_of_interest
                              ]);

                              return response()->json(["status"=>"success"]);
                        }
  
    }
    
    //process payments
    public function payment_processsor(Request $request){
        $carbondate=Carbon::now();
        $time=$carbondate->now('UTC')->setTimezone('WAT')->format('g:i:s a');
        $date=$carbondate->toDateString();
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
             
             
             if($paymentmode == "Card"){
                $cal_amount=(int)$course_fee * 100;
                $amount= $cal_amount;
                  if(isset($amount_inputed)){
                      $amount_to_pay=(int)$amount_inputed * 100;
                      $link="https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course_of_intrest}&amount={$amount_inputed}";
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
                $link="https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course_of_intrest}&amount={$amount}";
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
                        "payment_link"=>"https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course_of_intrest}&amount={$amount_inputed}"
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
                        "payment_link"=>"https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course_of_intrest}&amount={$course_fee}"
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
              $intrest_form_fee= 10000;
              $cal_amount= $intrest_form_fee * 100;
              $amount= $cal_amount;
              $course_of_intrest=$request->course_of_interest;
              $get_course= Courses::where('name',$course_of_intrest)->first();
              $course_fee=$get_course->course_fee;
             
             
             if($paymentmode == "Card"){
                $link="https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course_of_intrest}&amount={$intrest_form_fee}&type=interestform";
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
                    "payment_link"=>"https://pluralcode.academy/payment_verification?first_name={$first_name}&last_name={$last_name}&email={$email}&course={$course_of_intrest}&amount={$intrest_form_fee}&type=interestform"
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
                       $link="https://pluralcode.academy/payment_verification?email={$email}&course={$course}&amount={$amount_inputed}&type=payment_form";
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
                    $link="https://pluralcode.academy/payment_verification?email={$email}&course={$course}&amount={$course_fee}&type=payment_form";
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
                        "payment_link"=>"https://pluralcode.academy/payment_verification?email={$email}&course={$course}&amount={$amount_inputed}&type=payment_form"
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
                        "payment_link"=>"https://pluralcode.academy/payment_verification?email={$email}&course={$course}&amount={$course_fee}&type=payment_form"
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
