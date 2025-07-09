<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileTeacherRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return auth()->user()->role != "peserta_didik";
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		$userId = auth()->id();

		return [
			"firstname" => "required|string|max:255",
			"lastname" => "nullable|string|max:255",
			"email" => "required|email|unique:users,email,{$userId},id_user",
			"school" => "required|string|max:255",
			"nip" => "nullable|string|regex:/^[0-9]+$/",
			"birthplace" => "required|string|max:255",
			"birthdate" => "required|date|before:today",
			"contact" => "required|string|regex:/^[0-9]+$/|min:5|max:15",
			"gender" => "required|string|in:male,female",
		];
	}

	public function messages()
	{
		return [
			"firstname.required" => "Nama depan wajib diisi.",

			"school.required" => "Nama sekolah wajib diisi.",

			"email.required" => "Email wajib diisi.",
			"email.email" => "Format email tidak valid.",
			"email.unique" => "Email sudah digunakan.",

			"nip.string" => "NIP harus berupa teks.",
			"nip.regex" => "NIP hanya boleh berisi angka.",

			"birthplace.required" => "Tempat lahir wajib diisi.",
			"birthdate.required" => "Tanggal lahir wajib diisi.",
			"birthdate.before" => "Tanggal lahir harus sebelum hari ini.",

			"contact.required" => "Kontak wajib diisi.",
			"contact.string" => "Kontak harus berupa teks.",
			"contact.regex" => "Kontak hanya boleh berisi angka.",
			"contact.min" => "Kontak minimal terdiri dari 5 digit.",
			"contact.max" => "Kontak tidak boleh lebih dari 15 digit.",

			"gender.required" => "Jenis kelamin wajib dipilih.",
			"gender.in" => "Jenis kelamin tidak valid.",
		];
	}
}
