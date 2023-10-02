<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\QuoteController;
use App\Http\Controllers\Api\V1\TransactionController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/autenticate', function () {
        return response()->json([
            'message' => 'Unauthorized',
        ], 401);
    })->name('autenticate');

    Route::get('/quote', [QuoteController::class, 'getQuote']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/transaction/get', [TransactionController::class, 'getTrasaction']);
        Route::post('/transaction', [TransactionController::class, 'postTrasaction']);
    });
});
