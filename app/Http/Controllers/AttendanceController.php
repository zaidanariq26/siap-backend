<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
	public function createAttendance(CreateAttendanceRequest $request)
	{
		$validatedData = $request->validated();

		try {
			$user = Auth::user();

			$ongoingInternship = $user->internships->where("status", "ongoing")->first();

			if (!$ongoingInternship) {
				return response()->json(
					[
						"status" => "no_ongoing_internship",
						"message" => "Anda tidak memiliki data PKL yang sedang berlangsung.",
					],
					404
				);
			}

			DB::beginTransaction();

			$existingAttendance = $user
				->attendances()
				->whereDate("date", now()->toDateString())
				->first();

			$attendanceData = [
				"student_id" => $user->id_user,
				"internship_id" => $ongoingInternship->id_internship,
				"status" => $validatedData["status"],
				"time" => Carbon::now(config("app.timezone"))->format("H:i"),
				"date" => Carbon::now(config("app.timezone"))->toDateString(),
				"latitude" => $validatedData["latitude"],
				"longitude" => $validatedData["longitude"],
				"note" => $validatedData["note"] ?? null,
				"expired_at" => null,
			];

			$statusJournal = $attendanceData["status"] !== "present" ? "not_present" : "not_created";

			if ($existingAttendance) {
				if ($existingAttendance->status === "no_description") {
					$existingAttendance->update($attendanceData);
					$existingAttendance->journal->update(["status" => $statusJournal]);
					$attendance = $existingAttendance;
				} elseif ($existingAttendance->status === "off") {
					$existingAttendance->update($attendanceData);
					$attendance = $existingAttendance;

					Journal::create([
						"student_id" => $existingAttendance->student_id,
						"internship_id" => $existingAttendance->internship_id,
						"attendance_id" => $existingAttendance->id_attendance,
						"date" => $existingAttendance->date,
						"status" => $statusJournal,
					]);
				} else {
					DB::rollBack();
					return response()->json(
						[
							"message" => "Anda sudah melakukan presensi hari ini.",
						],
						409
					);
				}
			} else {
				$attendance = Attendance::create($attendanceData);

				Journal::create([
					"student_id" => $attendance->student_id,
					"internship_id" => $attendance->internship_id,
					"attendance_id" => $attendance->id_attendance,
					"date" => $attendance->date,
					"status" => $statusJournal,
				]);
			}

			DB::commit();

			return response()->json([
				"message" => "Presensi berhasil dicatat",
				"data" => $attendance,
			]);
		} catch (\Throwable $e) {
			DB::rollBack();
			Log::error($e);
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

			$attendances = Attendance::where("student_id", $userId)
				->where("status", "!=", "off")
				->whereHas("internship", function ($query) {
					$query->where("status", "ongoing");
				})
				->orderBy("created_at", "desc")
				->get();

			Log::info("Data", [$attendances]);

			return response()->json([
				"message" => "Data presensi berhasil didapatkan",
				"data" => $attendances,
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

	public function updateNoDescriptionAttendance(CreateAttendanceRequest $request, Attendance $attendance)
	{
		$validatedData = $request->validated();

		try {
			if (Carbon::now()->greaterThan($attendance->expired_at)) {
				return response()->json(
					[
						"status" => "attendance_expired",
						"message" => "Presensi sudah melewati batas waktu.",
					],
					403
				);
			}

			DB::beginTransaction();

			$attendanceData = [
				"status" => $validatedData["status"],
				"time" => Carbon::now(config("app.timezone"))->format("H:i"),
				"date" => $validatedData["date"] ?: Carbon::now(config("app.timezone"))->toDateString(),
				"latitude" => $validatedData["latitude"],
				"longitude" => $validatedData["longitude"],
				"note" => $validatedData["note"] ?? null,
				"expired_at" => null,
			];

			$statusJournal = $attendanceData["status"] !== "present" ? "not_present" : "not_created";

			$attendance->update($attendanceData);

			$journal = Journal::where("date", $attendance->date)->first();

			$journal->update([
				"status" => $statusJournal,
			]);

			DB::commit();

			return response()->json([
				"message" => "Presensi berhasil dicatat",
				"data" => $attendance,
			]);
		} catch (\Throwable $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat memperbarui data presensi. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}
}
