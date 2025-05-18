<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\InternshipController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Models\Attendance;
use Illuminate\Http\Request;

Route::middleware("auth")->group(function () {
	// Handle Teacher
	Route::get("/teachers/homerooms", [TeacherController::class, "getAllHomeroomTeachers"]);
	Route::get("/teachers", [TeacherController::class, "getAllTeachers"]);

	// Edit Profile
	Route::put("/students/profile/edit", [ProfileController::class, "editProfileStudent"])->middleware("checkRole:peserta_didik");

	// Handle User
	Route::get("/user", [UserController::class, "authUser"]);
	Route::delete("/user/delete", [UserController::class, "deleteUser"]);

	// Handle Internship
	Route::get("/internships", [InternshipController::class, "getAllInternships"])->middleware("checkRole:peserta_didik");
	Route::post("/internships/create", [InternshipController::class, "addStudentInternship"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
	Route::get("/internships/active-status-check", [InternshipController::class, "getOrUpdateActiveInternship"])->middleware("checkRole:peserta_didik");

	// Handle Attendance
	Route::post("/attendances/create", [AttendanceController::class, "createAttendance"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
	Route::get("/attendances", [AttendanceController::class, "getAllAttendances"])->middleware(["checkRole:peserta_didik"]);
});

require __DIR__ . "/auth.php";
