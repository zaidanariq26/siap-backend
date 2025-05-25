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
			$validatedData["user_id"] = $user->id_user;

			Internship::where("user_id", $user->id_user)
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
			$internship->loadMissing(["student:id_user,name,email,role", "teacher:id_user,name,email,role"]);
			$internship->makeHidden("updated_at", "created_at");

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

	public function getOrUpdateActiveInternship()
	{
		try {
			$userId = Auth::id();

			if (!$userId) {
				return response()->json(
					[
						"message" => "Pengguna belum login atau sesi telah berakhir.",
					],
					401
				);
			}

			$today = Carbon::today();

			$internship = Internship::where("user_id", $userId)
				->whereIn("status", ["pending", "ongoing"])
				->orderByDesc("start_date")
				->first();

			if ($internship) {
				$start = Carbon::parse($internship->start_date);
				$end = Carbon::parse($internship->end_date);

				if ($internship->status === "pending" && $today->between($start, $end)) {
					$internship->status = "ongoing";
					$internship->save();
				}

				$internship->loadMissing("student:id_user,name,email,role", "teacher:id_user,name,email,role");
				$internship->makeHidden("updated_at", "created_at");
			}

			return response()->json([
				"message" => "Data PKL berhasil didapatkan.",
				"data" => $internship,
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

	public function getAllInternships()
	{
		try {
			$userId = Auth::id();

			if (!$userId) {
				return response()->json(
					[
						"message" => "Pengguna belum login atau sesi telah berakhir.",
					],
					401
				);
			}

			$internships = Internship::where("user_id", $userId)->get();
			$internships->makeHidden(["created_at", "updated_at"]);

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
