<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Allaccesstoken;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use Validator;
class Authcontroller extends Controller
{
    public function admin_register(Request $request){
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
}
