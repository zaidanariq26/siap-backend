<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::controller(AuthController::class)->group(function () {
	Route::post("/register", "registerStudent");
	Route::post("/teacher/register", "registerTeacher");
	Route::post("/login", "login");
	Route::post("/forgot-password", "forgotPassword");
	Route::post("/reset-password", "resetPassword");
	Route::post("/logout", "logout")->middleware("auth");
});
