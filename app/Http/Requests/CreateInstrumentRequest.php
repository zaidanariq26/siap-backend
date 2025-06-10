<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInstrumentRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return auth()->user()->role === "kepala_program";
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			"major_id" => "required|exists:majors,id_major",
			"academic_year" => "required|string",
			"note" => "nullable|string",
			"instrument" => "required|array",
		];
	}

	public function messages()
	{
		return [
			"major_id.required" => "Jurusan wajib dipilih.",
			"major_id.exists" => "Jurusan yang dipilih tidak valid.",

			"academic_year.required" => "Tahun ajaran wajib diisi.",
			"academic_year.string" => "Format tahun ajaran harus berupa teks.",

			"note.string" => "Catatan harus berupa teks.",

			"instrument.required" => "Instrumen penilaian wajib diisi.",
			"instrument.array" => "Format instrumen penilaian tidak valid.",
		];
	}
}
