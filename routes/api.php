<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:sanctum")->group(function () {
	Route::get("/user", [AuthController::class, "authUser"]);
	Route::get("/get-all-homeroom-teacher", [TeacherController::class, "getAllHomeroomTeacher"]);

	// Edit Profile
	Route::put("students/profile/edit", [ProfileController::class, "editProfileStudent"]);
});
