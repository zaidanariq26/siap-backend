<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAttendanceRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return auth()->user()->role == "peserta_didik";
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		$rules = [
			"status" => "required|in:present,excused,sick",
			"latitude" => "required|numeric|between:-90,90",
			"longitude" => "required|numeric|between:-180,180",
			"accuracy" => "required",
			"date" => "nullable",
			"note" => "nullable|string",
			"attachment" => "nullable|file|mimes:jpeg,png,jpg|max:1024",
		];

		return $rules;
	}

	public function messages()
	{
		return [
			"status.required" => "Status kehadiran wajib diisi.",
			"status.in" => "Status kehadiran harus salah satu dari: hadir, izin, atau sakit.",

			"latitude.required" => "Latitude tidak tersedia",
			"latitude.numeric" => "Latitude harus berupa angka.",
			"latitude.between" => "Latitude harus berada antara -90 hingga 90.",

			"accuracy.required" => "Data akurasi tidak tersedia",

			"longitude.required" => "Longitude tidak tersedia.",
			"longitude.numeric" => "Longitude harus berupa angka.",
			"longitude.between" => "Longitude harus berada antara -180 hingga 180.",

			"note.string" => "Catatan harus berupa teks.",

			"attachment.file" => "Lampiran harus berupa file.",
			"attachment.mimes" => "Lampiran harus berupa file dengan format: jpeg, jpg, atau png.",
			"attachment.max" => "Ukuran lampiran tidak boleh lebih dari 1MB.",
		];
	}
}
