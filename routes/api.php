<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::group(['prefix' => 'user'], function() {

    /* guest routes */
    Route::post('register', 'API\AuthController@createUser');
    Route::post('login', 'API\AuthController@login');

    /* authenticated routes */
    Route::group(['middleware' => 'auth:sanctum'], function(){
        Route::get('details', 'API\AuthController@details');
        Route::post('loan/request/create', 'API\LoanRequestController@create');
        Route::get('loan/request/all', 'API\LoanRequestController@all');
        Route::post('loan/offer', 'API\LoanController@offerLoan');
        Route::get('loan/details/{loan_id}', 'API\LoanController@details');
        Route::get('loan/view', 'API\LoanController@viewOfferedLoans');
        Route::post('loan/respond', 'API\LoanController@respondOffer');
        Route::post('repayment/make', 'API\RepaymentController@makePayment');
    });
});  
