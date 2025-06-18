<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInstrumentRequest;
use App\Models\Instrument;
use App\Models\InstrumentItem;
use App\Models\InstrumentSection;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstrumentController extends Controller
{
	public function createInstrument(CreateInstrumentRequest $request)
	{
		$validatedData = $request->validated();

		try {
			DB::beginTransaction();

			$user = Auth::user();

			$instrument = Instrument::create([
				"major_id" => $validatedData["major_id"],
				"teacher_id" => $user->id_user,
				"status" => "not_applied",
				"academic_year" => $validatedData["academic_year"],
			]);

			foreach ($validatedData["instrument"] as $sectionData) {
				$section = InstrumentSection::create([
					"instrument_id" => $instrument->id_instrument,
					"section_code" => $sectionData["section_code"],
					"section_label" => $sectionData["section_label"],
				]);

				Log::info($section);

				foreach ($sectionData["items"] as $itemData) {
					InstrumentItem::create([
						"instrument_section_id" => $section->id_instrument_section,
						"item_code" => $itemData["item_code"],
						"item_label" => $itemData["item_label"],
					]);
				}
			}

			DB::commit();

			$instrument->loadMissing(["majorDetail", "instrumentSections", "instrumentSections.instrumentItems"]);

			return response()->json(
				[
					"message" => "Instrumen PKL Berhasil Dibuat",
					"data" => [$instrument],
				],
				201
			);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error("Instrumen Error= {$e}");

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat membuat instrumen PKL. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function updateInstrument(CreateInstrumentRequest $request, Instrument $instrument)
	{
		$validatedData = $request->validated();

		try {
			DB::beginTransaction();

			$user = Auth::user();

			$instrument->update([
				"major_id" => $validatedData["major_id"],
				"teacher_id" => $user->id_user,
				"academic_year" => $validatedData["academic_year"],
			]);

			$instrument->instrumentSections()->delete();

			foreach ($validatedData["instrument"] as $sectionData) {
				$section = InstrumentSection::create([
					"instrument_id" => $instrument->id_instrument,
					"section_code" => $sectionData["section_code"],
					"section_label" => $sectionData["section_label"],
				]);

				foreach ($sectionData["items"] as $itemData) {
					InstrumentItem::create([
						"instrument_section_id" => $section->id_instrument_section,
						"item_code" => $itemData["item_code"],
						"item_label" => $itemData["item_label"],
					]);
				}
			}

			DB::commit();

			$instrument->loadMissing(["majorDetail", "instrumentSections", "instrumentSections.instrumentItems"]);

			return response()->json([
				"message" => "Instrumen PKL Berhasil Diperbarui",
				"data" => [$instrument],
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error("Instrumen Error= {$e}");

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat mengedit instrumen PKL. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function getInstrument()
	{
		try {
			$user = Auth::user();

			$with = ["majorDetail", "instrumentSections", "instrumentSections.instrumentItems"];

			if ($user->role == "kepala_program") {
				$instruments = Instrument::with($with)->get();
			} else {
				$instruments = Instrument::with($with)->where("status", "applied")->get();
			}

			return response()->json([
				"data" => $instruments,
				"message" => "Data instrumen berhasil didapatkan.",
			]);
		} catch (\Throwable $e) {
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat mengambil data instrumen.",
					"error" => $e->getMessage(),
				],
				500
			);
		}
	}

	public function setInstrumentStatus(Request $request)
	{
		$validatedData = $request->validate([
			"status" => "required|in:applied,not_applied",
		]);

		try {
			$user = Auth::user()->loadMissing("majorLed");

			if ($user->role != "kepala_program") {
				return response()->json(
					[
						"message" => "Anda tidak memiliki izin untuk mengubah status instrumen.",
					],
					403
				);
			}

			if (!$user->teacher) {
				return response()->json(
					[
						"message" => "Data guru tidak ditemukan.",
					],
					404
				);
			}

			Instrument::where("major_id", $user->majorLed->id_major)->update(["status" => $validatedData["status"]]);

			$instruments = Instrument::with(["majorDetail", "instrumentSections", "instrumentSections.instrumentItems"])
				->where("major_id", $user->majorLed->id_major)
				->get();

			return response()->json([
				"data" => $instruments,
				"message" => "Status instrumen berhasil diubah menjadi '{$validatedData["status"]}'.",
			]);
		} catch (\Throwable $e) {
			\Log::error("Gagal mengubah status instrumen", ["error" => $e]);

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat mengubah status instrumen.",
					"error" => $e->getMessage(),
				],
				500
			);
		}
	}
}
