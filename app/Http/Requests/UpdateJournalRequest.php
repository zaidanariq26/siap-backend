<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJournalRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return auth()->user()->role === "peserta_didik";
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		$rules = [
			"date" => "required|date",
			"description" => "required|string",
			"is_new_image" => "required|boolean",
			"image_path" => "nullable",
		];

		if ($this->input("is_new_image")) {
			$rules["image_path"] = "required|image|mimes:jpeg,png,jpg|max:20480";
		}

		return $rules;
	}

	public function messages()
	{
		return [
			"date.required" => "Kolom tanggal wajib diisi.",
			"date.date" => "Kolom tanggal harus berupa tanggal yang valid.",
			"description.required" => "Kolom deskripsi wajib diisi.",
			"description.string" => "Kolom deskripsi harus berupa teks.",
			"image_path.required" => "Kolom lampiran wajib diisi.",
			"image_path.image" => "File yang diunggah harus berupa gambar.",
			"image_path.mimes" => "Format gambar harus jpeg, png, atau jpg.",
			"image_path.max" => "Ukuran gambar tidak boleh lebih dari 20 MB.",
		];
	}
}
