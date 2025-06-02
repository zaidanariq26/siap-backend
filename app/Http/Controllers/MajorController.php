<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
	public function getAllMajors()
	{
		try {
			$majors = Major::get(["id_major", "code", "name"]);

			return response()->json(
				[
					"message" => "Data jurusan berhasil didapatkan.",
					"data" => $majors,
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
