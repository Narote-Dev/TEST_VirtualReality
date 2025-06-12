<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\TransferController;


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

Route::middleware('auth:sanctum')->get('/User', function (Request $request) {
    return $request->user();
});

// user
    Route::post('/RegisterUser', [AuthController::class, 'register']);
    Route::get('/Login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->put('/UpdateProfile', [AuthController::class, 'updateProfile']);
// user

// Transaction 
    Route::get('/ShowTransaction', [TransactionController::class, 'index']);
    Route::get('/CheckTransaction/{id}', [TransactionController::class, 'show']);
    Route::post('/AddTransaction', [TransactionController::class, 'store']);
// Transaction 

//wallet
    Route::middleware('auth:sanctum')->get('/ShowWallet', [WalletController::class, 'index']);
    Route::middleware('auth:sanctum')->get('/SelWallet/{id}', [WalletController::class, 'show']);
    Route::middleware('auth:sanctum')->post('/CreateWallet', [WalletController::class, 'create']);
    Route::middleware('auth:sanctum')->post('/BalanceWallet/{id}', [WalletController::class, 'balance']);

//wallet

//Currency
    Route::get('/AllCurrency', [CurrencyController::class, 'index']);
    Route::get('/CheckCurrency/{id}', [CurrencyController::class, 'show']);
    Route::post('/AddCurrency', [CurrencyController::class, 'store']);
    Route::put('/UpdateCurrency/{id}', [CurrencyController::class, 'update']);
    Route::delete('/DelCurrency/{id}', [CurrencyController::class, 'destroy']);
//Currency

//tranfer
    // Route::post('/Tranfer', [TransferController::class, 'transfer']);
    Route::middleware('auth:sanctum')->post('/Tranfer', [TransferController::class, 'transfer']);

//tranfer
