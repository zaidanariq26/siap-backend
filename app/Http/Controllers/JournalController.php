<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateJournalRequest;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Storage;

class JournalController extends Controller
{
	public function getAllJournals()
	{
		try {
			$userId = Auth::id();

			$journals = Journal::where("student_id", $userId)
				->whereHas("internship", function ($query) {
					$query->where("status", "ongoing");
				})
				->with("attendance")
				->orderBy("created_at", "desc")
				->get();

			$journals->loadMissing([
				"attendance",
				"attendance.student",
				"attendance.student.student",
				"attendance.student.student.homeroomTeacher",
				"attendance.student.student.majorDetail",
			]);

			return response()->json([
				"message" => "Data jurnal berhasil didapatkan",
				"data" => $journals,
			]);
		} catch (\Throwable $e) {
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat mengambil data jurnal.",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function updateJournalById(UpdateJournalRequest $request, Journal $journal)
	{
		$validatedData = $request->validated();

		try {
			$userId = Auth::id();

			if ($journal->student_id !== $userId) {
				return response()->json(["message" => "Akses ditolak."], 403);
			}

			if (in_array($journal->attendance->status, ["no_description", "off"])) {
				return response()->json(
					[
						"message" => "Tidak dapat mengakses karena Anda belum melakukan presensi.",
						"status" => "attendance_missing",
					],
					403
				);
			}

			if ($request->hasFile("image_path")) {
				if ($journal->image_path) {
					Storage::disk("public")->delete($journal->image_path);
				}

				$imagePath = $request->file("image_path")->store("journal-documentation", "public");
				$validatedData["image_path"] = $imagePath;
			}

			DB::beginTransaction();

			$journal->update([
				"date" => $validatedData["date"],
				"captured_at" => $validatedData["captured_at"],
				"description" => $validatedData["description"],
				"image_path" => $validatedData["image_path"] ?? $journal->image_path,
				"status" => "in_review",
			]);

			$journal = $journal->fresh("attendance");
			DB::commit();

			return response()->json([
				"message" => "Data Jurnal Berhasil Didapatkan",
				"data" => $journal,
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat memperbarui jurnal. Silahkan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function reviewJournal(Request $request, Journal $journal)
	{
		$validatedData = $request->validate(
			[
				"status" => "required|string|in:approved,needs_revision",
			],
			[
				"status.required" => "Anda belum melakukan peninjauan.",
				"status.in" => "Status tidak valid.",
			]
		);

		try {
			$user = Auth::user();

			if ($journal->teacher_id !== $user->id_user) {
				return response()->json(["status" => "review_rejected", "message" => "Anda tidak memiliki akses untuk meninjau jurnal ini."], 403);
			}

			DB::beginTransaction();

			$journal->update([
				"status" => $validatedData["status"],
			]);

			$journal = $journal->fresh(with: "attendance");
			DB::commit();

			$message = "";
			if ($journal->status == "approved") {
				$message = "Jurnal Aktivitas Berhasil Disetujui";
			} elseif ($journal->status == "needs_revision") {
				$message = "Jurnal Aktivitas Berhasil Ditolak";
			}

			return response()->json([
				"message" => $message,
				"data" => $journal,
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat memperbarui jurnal. Silahkan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}
}
