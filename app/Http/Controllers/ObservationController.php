<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Observation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ObservationController extends Controller
{
	public function updateObservation(Request $request, Observation $observation)
	{
		$validatedData = $request->validate([
			"score" => "required|array",
			"note" => "nullable|string",
		]);

		if ($observation->status == "success") {
			return response()->json(
				[
					"message" => "Observasi sudah dilakukan dan tidak dapat diubah.",
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

			$observation->update([
				"status" => "success",
				"section_score" => json_encode($scores),
				"total_score" => $overallScore,
				"note" => $validatedData["note"] ?? null,
				"date" => Carbon::now(config("app.timezone"))->toDateString(),
			]);

			DB::commit();

			return response()->json([
				"message" => "Observasi Berhasil Dikirim",
				"data" => $observation,
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error("Observation Error= {$e}");

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat pengisian data observasi. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}
}
