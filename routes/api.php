<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('login', 'AuthenticationController@login');
Route::middleware('auth:sanctum')->post('/bankinformation', 'UserProfileController@setBankInformation');
Route::middleware('auth:sanctum')->get('/bankinformation', 'UserProfileController@getBankInformation');
Route::middleware('auth:sanctum')->get('/balance','UserProfileController@showBalance');
Route::middleware('auth:sanctum')->post('/withdraw','TransactionController@withdraw');
Route::middleware('auth:sanctum')->get('/bankAccount/{bankAccount}/transactions','TransactionController@showTransactionList');