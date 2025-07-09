<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileStudentRequest extends FormRequest
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
		$userId = auth()->id();

		return [
			"firstname" => "required|string|max:255",
			"lastname" => "nullable|string|max:255",
			"email" => "required|email|unique:users,email,{$userId},id_user",
			"school" => "required|string|max:255",
			"nisn" => "required|regex:/^[0-9]+$/|digits:10",
			"homeroom_teacher_id" => "required|integer|exists:users,id_user",
			"major_id" => "required|integer|exists:majors,id_major",
			"birthplace" => "required|string|max:255",
			"birthdate" => "required|date|before:today",
			"contact" => "required|string|regex:/^[0-9]+$/|min:10|max:15",
			"gender" => "required|string|in:male,female",
			"emergency_contact" => "required|string|regex:/^[0-9]+$/|min:10|max:15",
			"emergency_contact_name" => "required|string|max:255",
		];
	}

	public function messages()
	{
		return [
			"firstname.required" => "Nama depan wajib diisi.",
			"firstname.string" => "Nama depan harus berupa teks.",
			"firstname.max" => "Nama depan tidak boleh lebih dari 255 karakter.",

			"lastname.string" => "Nama belakang harus berupa teks.",
			"lastname.max" => "Nama belakang tidak boleh lebih dari 255 karakter.",

			"email.required" => "Email wajib diisi.",
			"email.email" => "Format email tidak valid.",
			"email.unique" => "Email sudah digunakan.",

			"school.required" => "Nama sekolah wajib diisi.",
			"school.string" => "Nama sekolah harus berupa teks.",
			"school.max" => "Nama sekolah tidak boleh lebih dari 255 karakter.",

			"nisn.required" => "NISN wajib diisi.",
			"nisn.regex" => "NISN hanya boleh berisi angka.",
			"nisn.digits" => "NISN harus terdiri dari 10 angka.",

			"homeroom_teacher_id.required" => "Wali kelas wajib dipilih.",
			"homeroom_teacher_id.integer" => "Wali kelas tidak valid.",
			"homeroom_teacher_id.exists" => "Wali kelas tidak ditemukan.",

			"major_id.required" => "Program keahlian wajib dipilih.",
			"major_id.integer" => "Program keahlian tidak valid.",
			"major_id.exists" => "Program keahlian tidak ditemukan.",

			"birthplace.required" => "Tempat lahir wajib diisi.",
			"birthplace.string" => "Tempat lahir harus berupa teks.",
			"birthplace.max" => "Tempat lahir tidak boleh lebih dari 255 karakter.",

			"birthdate.required" => "Tanggal lahir wajib diisi.",
			"birthdate.date" => "Tanggal lahir tidak valid.",
			"birthdate.before" => "Tanggal lahir harus sebelum hari ini.",

			"contact.required" => "Kontak wajib diisi.",
			"contact.string" => "Kontak harus berupa teks.",
			"contact.regex" => "Kontak hanya boleh berisi angka.",
			"contact.min" => "Kontak minimal terdiri dari 10 digit.",
			"contact.max" => "Kontak tidak boleh lebih dari 15 digit.",

			"gender.required" => "Jenis kelamin wajib dipilih.",
			"gender.string" => "Jenis kelamin harus berupa teks.",
			"gender.in" => "Jenis kelamin tidak valid.",

			"emergency_contact.required" => "Kontak darurat wajib diisi.",
			"emergency_contact.string" => "Kontak darurat harus berupa teks.",
			"emergency_contact.regex" => "Kontak darurat hanya boleh berisi angka.",
			"emergency_contact.min" => "Kontak darurat minimal terdiri dari 10 digit.",
			"emergency_contact.max" => "Kontak darurat tidak boleh lebih dari 15 digit.",

			"emergency_contact_name.required" => "Nama kontak darurat wajib diisi.",
			"emergency_contact_name.string" => "Nama kontak darurat harus berupa teks.",
			"emergency_contact_name.max" => "Nama kontak darurat tidak boleh lebih dari 255 karakter.",
		];
	}
}
