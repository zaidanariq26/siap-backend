<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
	public function getAllInternshipStudents()
	{
		try {
			$user = Auth::user();

			$internshipStudents = Internship::where("teacher_id", $user->id_user)->get();
			$internshipStudents->loadMissing(["student", "student.student", "attendances", "attendances.journal"]);

			return response()->json(
				[
					"message" => "Data peserta didik yang dibimbing berhasil didapatkan.",
					"data" => $internshipStudents,
				],
				200
			);
		} catch (\Exception $e) {
			Log::error($e);
			return response()->json(
				[
					"message" => "Gagal memuat data. Silakan refresh halaman dan coba lagi.",
					"error" => $e->getMessage(),
				],
				500
			);
		}
	}
}
