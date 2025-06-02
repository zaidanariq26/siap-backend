<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddInternshipRequest extends FormRequest
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
		return [
			"teacher_id" => "required|exists:users,id_user",
			"job_name" => "required|string|max:255",
			"company_name" => "required|string|max:255",
			"instructor_name" => "required|string|max:255",
			"instructor_contact" => "required|string|regex:/^[0-9]+$/|min:5|max:15",
			"teacher_contact" => "required|string|regex:/^[0-9]+$/|min:5|max:15",
			"start_date" => "required|date|after_or_equal:today",
			"end_date" => "required|date|after:start_date",
		];
	}

	public function messages()
	{
		return [
			"teacher_id.required" => "Kolom guru pembimbing wajib diisi.",
			"teacher_id.exists" => "Guru pembimbing yang dimasukkan tidak tersedia.",

			"job_name.required" => "Nama pekerjaan wajib diisi.",
			"job_name.string" => "Nama pekerjaan harus berupa teks.",
			"job_name.max" => "Nama pekerjaan tidak boleh lebih dari 255 karakter.",

			"company_name.required" => "Nama perusahaan wajib diisi.",
			"company_name.string" => "Nama perusahaan harus berupa teks.",
			"company_name.max" => "Nama perusahaan tidak boleh lebih dari 255 karakter.",

			"instructor_name.required" => "Nama instruktur wajib diisi.",
			"instructor_name.string" => "Nama instruktur harus berupa teks.",
			"instructor_name.max" => "Nama instruktur tidak boleh lebih dari 255 karakter.",

			"instructor_contact.required" => "Kontak guru pembimbing wajib diisi.",
			"instructor_contact.string" => "Kontak guru pembimbing harus berupa teks.",
			"instructor_contact.regex" => "Kontak guru pembimbing hanya boleh berisi angka.",
			"instructor_contact.min" => "Kontak guru pembimbing minimal terdiri dari 5 digit.",
			"instructor_contact.max" => "Kontak guru pembimbing tidak boleh lebih dari 15 digit.",

			"teacher_contact.required" => "Kontak guru pembimbing wajib diisi.",
			"teacher_contact.string" => "Kontak guru pembimbing harus berupa teks.",
			"teacher_contact.regex" => "Kontak guru pembimbing hanya boleh berisi angka.",
			"teacher_contact.min" => "Kontak guru pembimbing minimal terdiri dari 5 digit.",
			"teacher_contact.max" => "Kontak guru pembimbing tidak boleh lebih dari 15 digit.",

			"start_date.required" => "Tanggal mulai wajib diisi.",
			"start_date.date" => "Tanggal mulai harus berupa tanggal yang valid.",
			"start_date.after_or_equal" => "Tanggal mulai tidak boleh sebelum hari ini.",

			"end_date.date" => "Tanggal selesai harus berupa tanggal yang valid.",
			"end_date.after" => "Tanggal selesai harus setelah tanggal mulai.",
			"end_date.required" => "Tanggal selesai wajib diisi.",
		];
	}
}
