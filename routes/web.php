<?php

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AssesmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\InstrumentController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\ObservationController;

Route::middleware("auth")->group(function () {
	// Handle Teacher
	Route::get("/teachers/homerooms", [TeacherController::class, "getAllHomeroomTeachers"]);
	Route::get("/teachers", [TeacherController::class, "getAllTeachers"]);

	// Edit Profile
	Route::put("/students/profile/edit", [ProfileController::class, "editProfileStudent"])->middleware("checkRole:peserta_didik");
	Route::put("/teachers/profile/edit", [ProfileController::class, "editProfileTeacher"])->middleware("checkRole:guru_pembimbing,wali_kelas,kepala_program,manajemen_sekolah");

	// Handle User
	Route::get("/users/me", [UserController::class, "getAuthUser"]);
	Route::delete("/users/me", [UserController::class, "deleteMyAccount"]);
	Route::get("/users", [UserController::class, "getAllUsers"])->middleware("checkRole:manajemen_sekolah");
	Route::delete("/users/{user}/delete", [UserController::class, "deleteUserById"])->middleware("checkRole:manajemen_sekolah");
	Route::delete("/users/delete", [UserController::class, "deleteSelectedUsers"])->middleware("checkRole:manajemen_sekolah");
	Route::put("/users/{user}/remove-homeroom", [UserController::class, "removeFromHomeroom"])->middleware("checkRole:manajemen_sekolah");
	Route::put("/users/{user}/promote-homeroom", [UserController::class, "promoteToHomeroom"])->middleware("checkRole:manajemen_sekolah");
	Route::put("/users/{user}/promote-major-leader", [UserController::class, "promoteToMajorLeader"])->middleware("checkRole:manajemen_sekolah");
	Route::put("/users/{user}/promote-management", [UserController::class, "promoteToManagement"])->middleware("checkRole:manajemen_sekolah");

	// Handle Major
	Route::get("/majors", [MajorController::class, "getAllMajors"])->middleware("checkRole:peserta_didik,manajemen_sekolah");

	// Handle Internship
	Route::get("/internships/my", [InternshipController::class, "getMyInternships"])->middleware("checkRole:peserta_didik");
	Route::post("/internships/create", [InternshipController::class, "addStudentInternship"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
	Route::put("/internships/{internship}/update", [InternshipController::class, "updateStudentInternship"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
	Route::put("/internships/{internship}/set-completed", [InternshipController::class, "setInternshipCompleted"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
	Route::get("/internships/assigned", [InternshipController::class, "getAllInternshipsByRole"])->middleware(
		"checkRole:guru_pembimbing,wali_kelas,kepala_program,manajemen_sekolah"
	);
	Route::post("/internships/{internship}/assesments/create", [InternshipController::class, "createAssesment"])->middleware(
		"checkRole:guru_pembimbing,wali_kelas,kepala_program,manajemen_sekolah"
	);

	// Handle Attendance
	Route::post("/attendances/create", [AttendanceController::class, "createAttendance"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
	Route::put("/attendances/{attendance}/update", [AttendanceController::class, "updateNoDescriptionAttendance"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
	Route::get("/attendances/my", [AttendanceController::class, "getMyAttendances"])->middleware(["checkRole:peserta_didik"]);
	Route::get("/attendances/assigned", [AttendanceController::class, "getAllAttendancesByRole"])->middleware(
		"checkRole:guru_pembimbing,wali_kelas,kepala_program,manajemen_sekolah"
	);

	// Handle Journal
	Route::get("/journals/my", [JournalController::class, "getMyJournals"])->middleware(["checkRole:peserta_didik"]);
	Route::put("/journals/{journal}/update", [JournalController::class, "updateJournalById"])->middleware(["checkRole:peserta_didik", "checkProfile"]);
	Route::put("/internships/journals/{journal}/review", [JournalController::class, "reviewJournal"])->middleware(
		"checkRole:guru_pembimbing,wali_kelas,kepala_program,manajemen_sekolah"
	);

	// Handle Instrument
	Route::get("/instruments", [InstrumentController::class, "getInstrument"])->middleware("checkRole:guru_pembimbing,wali_kelas,kepala_program,manajemen_sekolah");
	Route::post("/instruments/create", [InstrumentController::class, "createInstrument"])->middleware(["checkRole:kepala_program"]);
	Route::put("/instruments/set-status", [InstrumentController::class, "setInstrumentStatus"])->middleware(["checkRole:kepala_program"]);
	Route::put("/instruments/{instrument}/update", [InstrumentController::class, "updateInstrument"])->middleware(["checkRole:kepala_program"]);

	// Handle Assesment
	Route::put("/assesments/{assesment}/update", [AssesmentController::class, "updateAssesment"])->middleware(
		"checkRole:guru_pembimbing,wali_kelas,kepala_program,manajemen_sekolah"
	);

	// Handle Observation
	Route::put("/observations/{observation}/update", [ObservationController::class, "updateObservation"])->middleware(
		"checkRole:guru_pembimbing,wali_kelas,kepala_program,manajemen_sekolah"
	);
});

require __DIR__ . "/auth.php";
