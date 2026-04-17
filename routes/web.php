<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ================= LANDING =================
Route::get('/', function () {
    return view('landing');
});

// ================= AUTH =================
Route::get('/login',[AuthController::class,'showLogin'])->name('login');
Route::post('/login',[AuthController::class,'login']);

Route::get('/register',[AuthController::class,'showRegister']);
Route::post('/register',[AuthController::class,'register']);

// ================= LOGOUT =================
Route::post('/logout',[AuthController::class,'logout']);