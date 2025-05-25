<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRegisterRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return false;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			"nisn" => "required|string|digits:10|exists:data_students,nisn",
			"email" => "required|email|unique:users,email",
			"password" => "required|confirmed|string|min:8",
			"firstname" => "required|string",
			"lastname" => "nullable|string",
			"npsn" => "required|string|digits:8",
			"school" => "required|string",
		];
	}

	public function messages(): array
	{
		return [
			// NISN
			"nisn.required" => "Kolom NISN wajib diisi.",
			"nisn.string" => "NISN harus berupa teks.",
			"nisn.digits" => "NISN harus terdiri dari 10 angka.",
			"nisn.exists" => "NISN yang Anda masukkan tidak ditemukan dalam data siswa.",

			// Email
			"email.required" => "Kolom email wajib diisi.",
			"email.email" => "Format email tidak valid.",
			"email.unique" => "Email sudah terdaftar.",

			// Password
			"password.required" => "Kolom password wajib diisi.",
			"password.confirmed" => "Konfirmasi password tidak sesuai.",
			"password.string" => "Password harus berupa teks.",
			"password.min" => "Password harus terdiri dari minimal 8 karakter.",

			// Firstname
			"firstname.required" => "Kolom nama depan wajib diisi.",
			"firstname.string" => "Nama depan harus berupa teks.",

			// Lastname (optional)
			"lastname.string" => "Nama belakang harus berupa teks.",

			// NPSN
			"npsn.required" => "Kolom NPSN wajib diisi.",
			"npsn.string" => "NPSN harus berupa teks.",
			"npsn.digits" => "NPSN harus terdiri dari 8 angka.",

			// School
			"school.required" => "Kolom nama sekolah wajib diisi.",
			"school.string" => "Nama sekolah harus berupa teks.",
		];
	}
}
