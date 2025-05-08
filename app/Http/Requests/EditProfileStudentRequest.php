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
		return auth()->user()->role === "peserta_didik";
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
			"nisn" => "required|digits:10",
			"major" => "required|string",
			"homeroom_teacher_id" => "required|integer|exists:users,id_user",
			"birthplace" => "required|string|max:255",
			"birthdate" => "required|date|before:today",
			"contact" => "required|string|max:20",
			"religion" => "required|string|in:islam,protestan,katolik,hindu,buddha,konghucu",
			"gender" => "required|string|in:male,female",
			"emergency_contact" => "required|string|max:20",
			"emergency_contact_name" => "required|string|max:255",
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
			"nisn.required" => "NISN wajib diisi.",
			"nisn.digits" => "NISN harus terdiri dari 10 angka.",
			"major.required" => "Program keahlian wajib dipilih.",
			"homeroom_teacher_id.required" => "Wali kelas wajib dipilih.",
			"homeroom_teacher_id.exists" => "Wali kelas tidak valid.",
			"birthplace.required" => "Tempat lahir wajib diisi.",
			"birthdate.required" => "Tanggal lahir wajib diisi.",
			"birthdate.before" => "Tanggal lahir harus sebelum hari ini.",
			"contact.required" => "Kontak wajib diisi.",
			"religion.required" => "Kolom agama wajib diisi.",
			"religion.in" => "Kolom agama tidak valid.",
			"gender.required" => "Jenis kelamin wajib dipilih.",
			"gender.in" => "Jenis kelamin tidak valid.",
			"emergency_contact.required" => "Kontak darurat wajib diisi.",
			"emergency_contact_name.required" => "Nama kontak darurat wajib diisi.",
		];
	}
}
