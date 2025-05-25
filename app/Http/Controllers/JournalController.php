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

			if (!$userId) {
				return response()->json(
					[
						"message" => "Pengguna belum login atau sesi telah berakhir.",
					],
					401
				);
			}

			$journals = Journal::where("user_id", $userId)
				->whereHas("internship", function ($query) {
					$query->where("status", "ongoing");
				})
				->with("attendance")
				->orderBy("created_at", "desc")
				->get();

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

			if ($journal->user_id !== $userId) {
				return response()->json(["message" => "Akses ditolak."], 403);
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
				"description" => $validatedData["description"],
				"image_path" => $validatedData["image_path"] ?? $journal->image_path,
				"status" => "in_review",
			]);

			$journal = $journal->fresh("attendance");
			DB::commit();

			return response()->json([
				"message" => "Data jurnal berhasil didapatkan",
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
