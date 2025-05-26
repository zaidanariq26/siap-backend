<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Internship;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddInternshipRequest;

class InternshipController extends Controller
{
	public function addStudentInternship(AddInternshipRequest $request)
	{
		$validatedData = $request->validated();

		try {
			$user = Auth::user();

			$validatedData["student_id"] = $user->id_user;

			Internship::where("student_id", $user->id_user)
				->whereIn("status", ["pending", "ongoing"])
				->update(["status" => "completed"]);

			$today = Carbon::today();
			$start = Carbon::parse($validatedData["start_date"]);
			$endDateExists = !empty($validatedData["end_date"]);

			if ($endDateExists) {
				$end = Carbon::parse($validatedData["end_date"]);

				if ($today->lt($start)) {
					$validatedData["status"] = "pending";
				} elseif ($today->between($start, $end)) {
					$validatedData["status"] = "ongoing";
				} else {
					$validatedData["status"] = "completed";
				}
			} else {
				if ($today->eq($start)) {
					$validatedData["status"] = "ongoing";
				} elseif ($today->lt($start)) {
					$validatedData["status"] = "pending";
				}
			}

			$internship = Internship::create($validatedData);
			$internship->loadMissing(["student", "teacher"]);

			DB::commit();

			return response()->json(
				[
					"message" => "Data PKL Anda telah berhasil disimpan",
					"data" => $internship,
				],
				201
			);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat membuat data PKL. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function getAllInternships()
	{
		try {
			$userId = Auth::id();

			$internships = Internship::where("student_id", $userId)->get();

			return response()->json([
				"message" => "Data PKL berhasil didapatkan",
				"data" => $internships,
			]);
		} catch (\Throwable $e) {
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat mengambil data PKL.",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}
}
