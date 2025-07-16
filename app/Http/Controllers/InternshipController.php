<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Journal;
use App\Models\Assesment;
use App\Models\Internship;
use App\Models\Observation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\AddInternshipRequest;

class InternshipController extends Controller
{
	public function addStudentInternship(AddInternshipRequest $request)
	{
		$validatedData = $request->validated();

		try {
			DB::beginTransaction();

			$user = Auth::user();
			$validatedData["student_id"] = $user->id_user;

			$ongoingOrPending = Internship::where("student_id", $user->id_user)
				->whereIn("status", ["ongoing", "pending"])
				->first();

			if ($ongoingOrPending) {
				return response()->json(
					[
						"status" => "active_internship_exists",
						"message" => "Anda masih memiliki PKL yang sedang berlangsung atau belum dimulai.",
					],
					409
				);
			}

			$today = Carbon::today();
			$start = Carbon::parse($validatedData["start_date"]);
			$end = Carbon::parse($validatedData["end_date"]);

			if ($today->lt($start)) {
				$validatedData["status"] = "pending";
			} elseif ($today->between($start, $end)) {
				$validatedData["status"] = "ongoing";
			} else {
				$validatedData["status"] = "completed";
			}

			$internship = Internship::create($validatedData);

			for ($i = 1; $i <= 6; $i++) {
				Observation::create([
					"internship_id" => $internship->id_internship,
					"name" => "Observasi Bulan Ke-$i",
					"status" => "needs_observation",
				]);
			}

			Assesment::create([
				"internship_id" => $internship->id_internship,
				"name" => "Asesmen 1",
				"status" => "needs_assesment",
			]);

			$internship->loadMissing(["student", "teacher"]);

			DB::commit();

			return response()->json(
				[
					"message" => "Data PKL Anda Telah Berhasil Disimpan",
					"data" => $internship,
				],
				201
			);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat menyimpan data PKL. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function updateStudentInternship(AddInternshipRequest $request, Internship $internship)
	{
		$validatedData = $request->validated();

		try {
			DB::beginTransaction();

			if ($internship->status == "completed") {
				return response()->json(
					[
						"status" => "internship_completed",
						"message" => "Data tidak dapat diubah karena status PKL telah diselesaikan.",
					],
					409
				);
			}

			$today = Carbon::today();
			$start = Carbon::parse($validatedData["start_date"]);
			$end = Carbon::parse($validatedData["end_date"]);

			if ($today->lt($start)) {
				$validatedData["status"] = "pending";
			} elseif ($today->between($start, $end)) {
				$validatedData["status"] = "ongoing";
			} else {
				$validatedData["status"] = "completed";
			}

			$internship->update($validatedData);
			$internship->loadMissing(["student", "teacher"]);

			DB::commit();

			return response()->json(
				[
					"message" => "Data PKL Anda Telah Berhasil Diperbarui",
					"data" => $internship,
				],
				200
			);
		} catch (\Exception $e) {
			DB::rollBack();

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat memperbarui data PKL. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function setInternshipCompleted(Internship $internship)
	{
		try {
			if (auth()->id() != $internship->student_id) {
				return response()->json(
					[
						"message" => "Anda tidak memiliki izin untuk menyelesaikan PKL ini.",
					],
					403
				);
			}

			if ($internship->status == "completed") {
				return response()->json(
					[
						"status" => "internship_completed",
						"message" => "Data tidak dapat diubah karena status PKL telah diselesaikan.",
					],
					409
				);
			}

			if ($internship->status == "pending") {
				return response()->json(
					[
						"status" => "internship_not_started",
						"message" => "Anda belum bisa menyelesaikan PKL sebelum tanggal dimulai.",
					],
					409
				);
			}

			DB::beginTransaction();

			$internship->update([
				"status" => "completed",
			]);

			$internship->loadMissing(["student", "teacher"]);

			DB::commit();

			return response()->json(
				[
					"message" => "Anda Telah Berhasil Menyelesaikan PKL",
					"data" => $internship,
				],
				200
			);
		} catch (\Exception $e) {
			DB::rollBack();

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat memperbarui data PKL. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function getMyInternships()
	{
		try {
			$userId = Auth::id();

			$internships = Internship::with("teacher")->where("student_id", $userId)->get();

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

	public function getAllInternshipsByRole()
	{
		try {
			$user = Auth::user();

			if ($user->role == "wali_kelas") {
				$internships = Internship::where(function ($internshipQuery) use ($user) {
					$internshipQuery
						->whereHas("student.student", function ($studentQuery) use ($user) {
							$studentQuery->where("homeroom_teacher_id", $user->id_user);
						})
						->orWhere("teacher_id", $user->id_user);
				})->get();
			} elseif ($user->role == "kepala_program") {
				$internships = Internship::where(function ($internshipQuery) use ($user) {
					$internshipQuery
						->whereHas("student.student", function ($studentQuery) use ($user) {
							$studentQuery->where("major_id", $user->majorLed->id_major);
						})
						->orWhere("teacher_id", $user->id_user);
				})->get();
			} elseif ($user->role == "manajemen_sekolah") {
				$internships = Internship::all();
			} else {
				$internships = Internship::where("teacher_id", $user->id_user)->get();
			}

			$internships->loadMissing([
				"student",
				"teacher",
				"student.student",
				"student.student.homeroomTeacher",
				"student.student.majorDetail",
				"attendances",
				"attendances.student",
				"attendances.student.student",
				"attendances.student.student.majorDetail",
				"journals",
				"journals.attendance",
				"assesments",
				"observations",
			]);

			Log::info("Data", [$internships]);

			return response()->json([
				"message" => "Data peserta didik berhasil didapatkan",
				"data" => $internships,
			]);
		} catch (\Throwable $e) {
			Log::error("Error", [$e]);

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat mengambil data presensi.",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function createAssesment(Internship $internship)
	{
		try {
			DB::beginTransaction();
			$assessmentCount = $internship->assesments()->count() + 1;

			$assesment = Assesment::create([
				"internship_id" => $internship->id_internship,
				"name" => "Asesmen {$assessmentCount}",
				"status" => "needs_assesment",
			]);

			DB::commit();

			return response()->json(
				[
					"message" => "Asesment Telah Berhasil Ditambahkan",
					"data" => $assesment,
				],
				200
			);
		} catch (\Exception $e) {
			DB::rollBack();

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat menambahkan asesmen. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}
}
