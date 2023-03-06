<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/resend', [AuthController::class, 'resend']);
Route::post('/verify', [AuthController::class, 'verify']);
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetRequest']);
Route::post('/reset-password', [AuthController::class, 'passwordReset']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('logout');
// Route::get('verification/verify/{token}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    // dd("3");
    return $request->user();
});


Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verifyEmail'])
->middleware(['signed', 'throttle:2,1'])
->name('verification.verify');
