<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courses;
use App\Models\Enrollments;
use App\Models\Transaction;
use App\Models\Intrestform;
class Paymentprocessor extends Controller
{   
    //pay with paysatck
    public function initialize_payments($email,$amount){
        $url = "https://api.paystack.co/transaction/initialize";
        $fields = [
          'email' => $email,
          'amount' => $amount,
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
    
    //process payments
    public function payment_processsor(Request $request){
        
        $request_type= $request->type;
        if($request_type= "enrollment"){
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email'=>'required',
                'mode_of_learning' => 'required',
                'course_of_interest'=>'required',
                'mode_of_payment' => 'required',
                'payment_status'=>'required'
            ]);
    
            if($validator->fails()){
                return response()->json(['error' => $validator->errors()->all()]);
            }else{
                $email=$request->email;
                $course_of_intrest=$request->course_of_interest;
                $get_course= Courses::where('name',$course_of_intrest)->first();
                $amount= $get_course->course_fee;
                
                //initialize and verify payments
                $payment=$this->initialize_payments($email,$amount);
                $paystack_response=json_decode($payment);
                if($paystack_response->status == true){
                    $reference=$paystack_response->data->reference;
                    $verify_payment=$this->verify_payments($reference);
                    $verify_payment_response= json_decode($verify_payment);
                    if($verify_payment_response->status == true){
                        Enrollments::create([
                            'name'=> $request->name,
                            'email'=>$request->email,
                            'mode_of_learning'=>$request->mode_of_learning,
                            'course_of_interest'=>$request->course_of_interest,
                            'mode_of_payment'=>$request->mode_of_payment,
                            'payment_status'=>$request->payment_status
                        ]); 
                    }
                }else{

                }
              
            }
        }
      
    }
}
