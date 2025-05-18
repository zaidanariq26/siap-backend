<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAttendanceRequest;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
	public function createAttendance(CreateAttendanceRequest $request)
	{
		$validatedData = $request->validated();

		try {
			$user = Auth::user();

			$ongoingInternship = $user->studentInternships->where("status", "ongoing")->first();

			if (!$ongoingInternship) {
				return response()->json(
					[
						"message" => "Anda tidak memiliki data PKL yang sedang berlangsung.",
					],
					400
				);
			}

			$alreadyAttended = $user
				->attendances()
				->whereDate("created_at", now()->toDateString())
				->exists();

			if ($alreadyAttended) {
				return response()->json(
					[
						"message" => "Anda sudah melakukan presensi hari ini.",
					],
					409
				);
			}

			DB::beginTransaction();

			$attendance = Attendance::create([
				"student_id" => $user->id_user,
				"internship_id" => $ongoingInternship->id_internship,
				"status" => $validatedData["status"],
				"time" => Carbon::now(config("app.timezone"))->format("H:i"),
				"date" => Carbon::now(config("app.timezone"))->toDateString(),
				"latitude" => $validatedData["latitude"],
				"longitude" => $validatedData["longitude"],
				"note" => $validatedData["note"] ?? null,
			]);

			DB::commit();

			$attendance->makeHidden(["created_at", "updated_at"]);

			return response()->json([
				"message" => "Presensi berhasil dicatat.",
				"data" => $attendance,
			]);
		} catch (\Throwable $e) {
			DB::rollBack();
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat membuat data presensi. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function getAllAttendances()
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

			$attendances = Attendance::where("student_id", $userId)->get();
			$attendances->makeHidden(["created_at", "updated_at"]);

			return response()->json([
				"message" => "Data presensi berhasil didapatkan",
				"data" => $attendances,
			]);
		} catch (\Throwable $e) {
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat mengambil data presensi.",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}
}
