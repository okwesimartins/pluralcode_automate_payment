<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\Paymentprocessor;
use App\Http\Controllers\Enrolledstudents;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('register_admin',[Authcontroller::class, 'register_admin']);
Route::post('admin_login',[Authcontroller::class, 'admin_login']);
Route::post('payments',[Paymentprocessor::class, 'payment_processsor']);
Route::post('enrolled_students',[Enrolledstudents::class, 'getstudents']);
Route::group(['middleware'=>'auth:api','prefix'=>'admin'],
   function(){
   
   });