<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::controller(AuthController::class)->group(function () {
	Route::post("/register", "registerStudent")->middleware("onlyGuest");
	// Route::post("/register/teacher", "registerStudent");
	Route::post("/login", "loginStudent")->middleware("onlyGuest");
	Route::post("/forgot-password", "forgotPassword")->middleware("onlyGuest");
	Route::post("/reset-password", "resetPassword")->middleware("onlyGuest");
	Route::post("/logout", "logout")->middleware("auth");
});
