<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
	public function getAllHomeroomTeacher()
	{
		try {
			$homerooms = User::where("role", "wali_kelas")->get();
			$homerooms->makeHidden(["email_verified_at", "created_at", "updated_at"]);
			return response()->json(
				[
					"code" => 200,
					"data" => $homerooms,
				],
				200
			);
		} catch (\Exception $e) {
			return response()->json(
				[
					"code" => 500,
					"message" => "Gagal memuat data. Silakan refresh halaman dan coba lagi.",
					"error" => $e->getMessage(),
				],
				500
			);
		}
	}
}
