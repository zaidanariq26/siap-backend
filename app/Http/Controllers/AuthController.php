<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
	public function registerStudent(Request $request)
	{
		$validatedData = $request->validate(
			[
				"nisn" => "required|string|digits:10|exists:data_students,nisn",
				"email" => "required|email|unique:users,email",
				"password" => "required|confirmed|string|min:8",
				"firstname" => "required|string",
				"lastname" => "nullable|string",
				"npsn" => "required|string|digits:8",
				"school" => "required|string",
			],
			[
				"nisn.required" => "Kolom NISN wajib diisi.",
				"nisn.digits" => "NISN harus terdiri dari 10 angka.",
				"nisn.exists" => "NISN yang Anda masukkan tidak ditemukan.",
				"firstname.required" => "Kolom Nama depan wajib diisi.",
				"npsn.required" => "Kolom NPSN wajib diisi.",
				"npsn.digits" => "NPSN harus terdiri dari 8 angka.",
				"school.required" => "Kolom nama sekolah wajib diisi.",
				"password.min" => "Password harus terdiri dari minimal 8 karakter.",
			]
		);

		try {
			DB::beginTransaction();

			$fullName = $validatedData["firstname"];

			if (!empty($validatedData["lastname"])) {
				$fullName .= " " . $validatedData["lastname"];
			}

			$user = User::create([
				"name" => $fullName,
				"email" => $validatedData["email"],
				"password" => Hash::make($validatedData["password"]),
				"email_verified_at" => now(),
				"role" => "peserta_didik",
			]);

			$firstInitial = strtoupper(substr($validatedData["firstname"], 0, 1));
			$lastInitial = !empty($validatedData["lastname"]) ? strtoupper(substr($validatedData["lastname"], 0, 1)) : "";
			$initials = $firstInitial . $lastInitial;
			$avatarUrl = "https://api.dicebear.com/9.x/initials/svg?seed=" . urlencode($initials);

			Student::create([
				"user_id" => $user->id_user,
				"firstname" => $validatedData["firstname"],
				"lastname" => $validatedData["lastname"] ?? "",
				"nisn" => $validatedData["nisn"],
				"npsn" => $validatedData["npsn"],
				"avatar" => $avatarUrl,
				"school" => $validatedData["school"],
			]);

			DB::commit();

			Auth::login($user);
			$userId = Auth::id();

			$user = Cache::store("database")->remember("auth_user_{$userId}", now()->addMinutes(120), function () use ($userId) {
				$user = Auth::user()->loadMissing(["student", "student.homeroomTeacher:id_user,name,email,role"]);
				$user->student->makeHidden(["created_at", "updated_at"]);
				$user->makeHidden(["email_verified_at", "created_at", "updated_at", "deleted_at"]);
				return $user;
			});

			return response()->json(["message" => "Registrasi berhasil", "data" => $user], 201);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat register. Silahkan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function loginStudent(Request $request)
	{
		$credentials = $request->validate([
			"email" => ["required", "email"],
			"password" => ["required"],
		]);
		try {
			if (Auth::attempt($credentials)) {
				$request->session()->regenerate();
				$userId = Auth::id();

				Cache::forget("auth_user_{$userId}");
				$user = Cache::remember("auth_user_{$userId}", now()->addHours(2), function () use ($userId) {
					$user = Auth::user()->loadMissing(["student", "student.homeroomTeacher:id_user,name,email,role"]);
					$user->student->makeHidden(["created_at", "updated_at"]);
					$user->makeHidden(["email_verified_at", "created_at", "updated_at", "deleted_at"]);
					return $user;
				});

				return response()->json(
					[
						"message" => "Login berhasil!",
						"data" => $user,
					],
					200
				);
			}
			return response()->json(
				[
					"message" => "Email dan kata sandi tidak valid.",
				],
				401
			);
		} catch (\Throwable $e) {
			return response()->json(
				[
					"code" => 500,
					"message" => "Terjadi kesalahan saat login. Silahkan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function logout(Request $request)
	{
		$userId = Auth::id();
		Cache::forget("auth_user_{$userId}");
		Auth::guard("web")->logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		return response()->json([
			"message" => "Logout sukses",
		]);
	}
}
