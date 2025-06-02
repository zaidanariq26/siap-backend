<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
	public function getAllHomeroomTeachers()
	{
		try {
			$homerooms = User::where("role", "wali_kelas")->get();
			return response()->json(
				[
					"message" => "Data wali kelas berhasil didapatkan.",
					"data" => $homerooms,
				],
				200
			);
		} catch (\Exception $e) {
			return response()->json(
				[
					"message" => "Gagal memuat data. Silakan refresh halaman dan coba lagi.",
					"error" => $e->getMessage(),
				],
				500
			);
		}
	}

	public function getAllTeachers()
	{
		try {
			$teachers = User::whereNot("role", "peserta_didik")->get();

			return response()->json(
				[
					"message" => "Data guru pembimbing berhasil didapatkan.",
					"data" => $teachers,
				],
				200
			);
		} catch (\Exception $e) {
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
