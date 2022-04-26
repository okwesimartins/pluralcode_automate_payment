<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Allaccesstoken;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use Validator;
class Authcontroller extends Controller
{    
    //register admin
    public function register_admin(Request $request){
        $rule= [
          'email'=>'required',
          'password'=>'required|min:6'
        ];
        $validator= Validator::make($request->all(),$rule);
        if($validator->fails()){
            return response()->json($validator->errors());
        }else{
            User::create([
              "email"=>$request->email,
              "password"=>Hash::make($request->password)

            ]);

            return response()->json(["message"=>"success"]);
        }
    }

    //admin login 
    public function admin_login(Request $request){
        $validator = Validator::make($request->all(), [
              'email' => 'required',
              'password'=>'required'
          ]);
  
          if($validator->fails()){
              return response()->json(['error' => $validator->errors()->all()]);
          }else{
            
              if(auth()->attempt(['email' => request('email'), 'password' => request('password')])){
           
              $admin =auth()->user();
              
              $success =  $admin;
              
            $ip_address=$request->ip();
            $admin_tokens=$admin->accessTokens($ip_address);
          
           if( $admin_tokens->count() > 0){
               
              $admin->deletetoken($ip_address);
           }
           $token_object=$admin->createToken('admintoken');
           $accesstoken=$token_object->accessToken;
           $success['token'] = $accesstoken;
           $tokenid= $token_object->token->id;
            
          
           Allaccesstoken::where('id', $tokenid)->update([
               "ip_address"=> $ip_address
               ]);
  
              return response()->json($success, 200);
          
              }
              else{
                   return response()->json([
                      "status"=>"failed",
                      "message"=>"Invalid email or password"
                      ]);
              }
            
          }
  }
}
