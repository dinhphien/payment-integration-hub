<?php

use App\Http\Controllers\Loan\ApproveLoanController;
use App\Http\Controllers\Loan\CreateLoanController;
use App\Http\Controllers\Loan\Repayment\AddRepaymentController;
use App\Http\Controllers\Loan\ViewLoanController;
use App\Http\Controllers\V1\Cafe24CheckoutController;
use App\Http\Controllers\V1\ShopifyCheckoutController;
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

Route::group([
    'prefix' => 'v1'
], function (){
    Route::group([
        'prefix' => 'checkout'
    ], function () {
        Route::post('/cafe24', Cafe24CheckoutController::class);
        Route::post('/shopify', ShopifyCheckoutController::class);
    });
});
