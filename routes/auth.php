<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::controller(AuthController::class)->group(function () {
	Route::post("/register", "registerStudent");
	// Route::post("/register/teacher", "registerStudent");
	Route::post("/login", "loginStudent");
	Route::post("/forgot-password", "forgotPassword");
	Route::post("/reset-password", "resetPassword");
	Route::post("/logout", "logout")->middleware("auth");
});
