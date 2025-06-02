<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\MajorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Models\Attendance;
use Illuminate\Http\Request;

Route::middleware("auth")->group(function () {
	// Handle Teacher
	Route::get("/teachers/homerooms", [TeacherController::class, "getAllHomeroomTeachers"]);
	Route::get("/teachers", [TeacherController::class, "getAllTeachers"]);

	// Handle Student
	// Route::get("/students/internship-students", [StudentController::class, "getAllInternshipStudents"]);

	// Edit Profile
	Route::put("/students/profile/edit", [ProfileController::class, "editProfileStudent"])->middleware("checkRole:peserta_didik");
	Route::put("/teachers/profile/edit", [ProfileController::class, "editProfileTeacher"])->middleware("checkRole:guru_pembimbing,wali_kelas,kepala_program,manajemen_sekolah");

	// Handle User
	Route::get("/user", [UserController::class, "authUser"]);
	Route::delete("/user/delete", [UserController::class, "deleteUser"]);

	// Handle Major
	Route::get("/majors", [MajorController::class, "getAllMajors"]);

	// Handle Internship
	Route::get("/internships", [InternshipController::class, "getAllInternships"])->middleware("checkRole:peserta_didik");
	Route::post("/internships/create", [InternshipController::class, "addStudentInternship"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
	Route::put("/internships/{internship}/update", [InternshipController::class, "updateStudentInternship"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
	Route::put("/internships/{internship}/set-completed", [InternshipController::class, "setInternshipCompleted"])->middleware(["checkRole:peserta_didik", "checkProfile"]);

	Route::get("/internships/students", [InternshipController::class, "getStudentsByRole"])->middleware("checkRole:wali_kelas,kepala_program,manajemen_sekolah");
	Route::get("/internships/mentees", [InternshipController::class, "getAllMentees"])->middleware("checkRole:guru_pembimbingwali_kelas,kepala_program,manajemen_sekolah");

	// Handle Attendance
	Route::post("/attendances/create", [AttendanceController::class, "createAttendance"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
	Route::put("/attendances/{attendance}/update", [AttendanceController::class, "updateNoDescriptionAttendance"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
	Route::get("/attendances", [AttendanceController::class, "getAllAttendances"])->middleware(["checkRole:peserta_didik"]);
	Route::get("/students/internships/attendances", [AttendanceController::class, "getAllStudentAttendances"])->middleware(
		"checkRole:guru_pembimbing,wali_kelas,kepala_program,manajemen_sekolah"
	);

	// Handle Journal
	Route::get("/journals", [JournalController::class, "getAllJournals"])->middleware(["checkRole:peserta_didik"]);
	Route::put("/journals/{journal}/update", [JournalController::class, "updateJournalById"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
});

require __DIR__ . "/auth.php";
