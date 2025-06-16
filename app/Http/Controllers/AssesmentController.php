<?php

namespace App\Http\Controllers;

use App\Models\Assesment;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssesmentController extends Controller
{
	public function updateAssesment(Request $request, Assesment $assesment)
	{
		$validatedData = $request->validate([
			"score" => "required|array",
			"note" => "nullable|string",
		]);

		if ($assesment->status === "success") {
			return response()->json(
				[
					"message" => "Penilaian sudah dilakukan dan tidak dapat diubah.",
				],
				403
			);
		}

		try {
			DB::beginTransaction();

			$scores = [];
			$totalScore = 0;
			$totalItems = 0;

			foreach ($validatedData["score"] as $sectionScores) {
				$sectionTotal = array_sum($sectionScores);
				$sectionCount = count($sectionScores);
				$sectionScore = $sectionCount > 0 ? ($sectionTotal / $sectionCount) * 100 : 0;

				$scores[] = round($sectionScore, 1);

				$totalScore += $sectionTotal;
				$totalItems += $sectionCount;
			}

			$overallScore = $totalItems > 0 ? array_sum($scores) / count($scores) : 0;
			$overallScore = round($overallScore, 1);

			$assesment->update([
				"status" => "success",
				"section_score" => json_encode($scores),
				"total_score" => $overallScore,
				"note" => $validatedData["note"] ?? null,
				"date" => Carbon::now(config("app.timezone"))->toDateString(),
			]);

			DB::commit();

			return response()->json([
				"message" => "Observasi Berhasil Dikirim",
				"data" => $assesment,
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error("Assesment Error= {$e}");

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat pengisian data asesmen. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}
}
