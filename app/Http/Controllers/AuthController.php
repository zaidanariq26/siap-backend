<?php

namespace App\Http\Controllers;

use App\Helpers\SlugHelper;
use App\Http\Requests\StudentRegisterRequest;
use App\Http\Requests\TeacherRegisterRequest;
use App\Jobs\SendResetPasswordMail;
use App\Mail\ResetPasswordMail;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
	public function registerStudent(StudentRegisterRequest $request)
	{
		$validatedData = $request->validated();

		try {
			DB::beginTransaction();

			$fullName = $validatedData["firstname"];

			if (!empty($validatedData["lastname"])) {
				$fullName .= " " . $validatedData["lastname"];
			}

			$slug = SlugHelper::generateUniqueSlug(User::class, $fullName);

			$user = User::create([
				"name" => $fullName,
				"email" => $validatedData["email"],
				"slug" => $slug,
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

			$user = Cache::store("database")->remember("auth_user_{$user->id_user}", now()->addMinutes(120), function () use ($user) {
				$user = $user->loadMissing(["student", "student.homeroomTeacher", "student.majorDetail"]);
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

	public function registerTeacher(TeacherRegisterRequest $request)
	{
		$validatedData = $request->validated();

		try {
			DB::beginTransaction();

			$fullName = $validatedData["firstname"];

			if (!empty($validatedData["lastname"])) {
				$fullName .= " " . $validatedData["lastname"];
			}

			$slug = SlugHelper::generateUniqueSlug(User::class, $fullName);

			$user = User::create([
				"name" => $fullName,
				"email" => $validatedData["email"],
				"slug" => $slug,
				"password" => Hash::make($validatedData["password"]),
				"email_verified_at" => now(),
				"role" => "guru_pembimbing",
			]);

			$firstInitial = strtoupper(substr($validatedData["firstname"], 0, 1));
			$lastInitial = !empty($validatedData["lastname"]) ? strtoupper(substr($validatedData["lastname"], 0, 1)) : "";
			$initials = $firstInitial . $lastInitial;
			$avatarUrl = "https://api.dicebear.com/9.x/initials/svg?seed=" . urlencode($initials);

			Teacher::create([
				"user_id" => $user->id_user,
				"firstname" => $validatedData["firstname"],
				"lastname" => $validatedData["lastname"] ?? "",
				"npsn" => $validatedData["npsn"],
				"avatar" => $avatarUrl,
				"school" => $validatedData["school"],
			]);

			DB::commit();

			Auth::login($user);

			$user = Cache::store("database")->remember("auth_user_{$user->id_user}", now()->addMinutes(120), function () use ($user) {
				$user = $user->loadMissing(["teacher"]);
				return $user;
			});

			return response()->json(["message" => "Registrasi berhasil", "data" => $user], 201);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat register. Silahkan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function login(Request $request)
	{
		$credentials = $request->validate(
			[
				"email" => ["required", "email"],
				"password" => ["required"],
			],
			[
				"email.required" => "Email wajib diisi.",
				"email.email" => "Format email tidak valid.",
				"password.required" => "Password wajib diisi.",
			]
		);

		try {
			if (Auth::attempt($credentials)) {
				$request->session()->regenerate();
				$user = Auth::user();

				Cache::forget("auth_user_{$user->id_user}");

				$dataUser = Cache::remember("auth_user_{$user->id_user}", now()->addHours(2), function () use ($user) {
					if ($user->role == "peserta_didik") {
						return $user->loadMissing(["student", "student.homeroomTeacher", "student.majorDetail"]);
					} elseif ($user->role == "kepala_program") {
						return $user->loadMissing(["teacher", "majorLed"]);
					}

					return $user->loadMissing(["teacher"]);
				});

				return response()->json([
					"message" => "Login Berhasil",
					"data" => $dataUser,
				]);
			}
			return response()->json(
				[
					"message" => "Email dan kata sandi tidak valid.",
				],
				401
			);
		} catch (\Throwable $e) {
			Log::error($e);
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat login. Silahkan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function forgotPassword(Request $request)
	{
		$validatedData = $request->validate(
			[
				"email" => "required|email|exists:users,email",
			],
			[
				"email.required" => "Email harus diisi.",
				"email.email" => "Format email tidak valid.",
				"email.exists" => "Email tidak ditemukan.",
			]
		);

		try {
			DB::table("password_reset_tokens")->where("email", $validatedData["email"])->delete();

			$user = User::where("email", $validatedData["email"])->first();

			$resetToken = bin2hex(random_bytes(32));

			DB::table("password_reset_tokens")->insert([
				"email" => $validatedData["email"],
				"token" => $resetToken,
				"created_at" => Carbon::now(),
			]);

			$prefixRole = $user->role != "peserta_didik" ? "/guru" : "";

			$resetLink = env("FRONTEND_URL") . "{$prefixRole}/reset-password?token={$resetToken}&email=" . urlencode($user->email);

			Log::info("tes", [
				"resetToken" => $resetToken,
				"link" => $resetLink,
			]);

			SendResetPasswordMail::dispatch($user->email, $resetLink, $user->name);

			return response()->json([
				"message" => "Silakan periksa kotak masuk atau folder spam Anda dan ikuti petunjuk yang diberikan untuk mengatur ulang kata sandi Anda.",
			]);
		} catch (\Throwable $th) {
			Log::error("Forgot password error: " . $th->getMessage());

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat mengirim link reset password. Silakan coba lagi nanti.",
					"error" => app()->environment("local") ? $th->getMessage() : null,
				],
				500
			);
		}
	}

	public function resetPassword(Request $request)
	{
		$validatedData = $request->validate(
			[
				"email" => "required|email|exists:users,email",
				"token" => "required",
				"password" => "required|min:8|confirmed",
			],
			[
				"email.required" => "Email harus diisi.",
				"email.email" => "Format email tidak valid.",
				"email.exists" => "Email tidak ditemukan.",

				"token.required" => "Token reset password diperlukan.",

				"password.required" => "Password harus diisi.",
				"password.min" => "Password minimal 8 karakter.",
				"password.confirmed" => "Konfirmasi password tidak cocok.",
			]
		);

		try {
			$dataToken = DB::table("password_reset_tokens")->where("token", operator: $validatedData["token"])->first();

			if (!$dataToken) {
				return response()->json(
					[
						"message" => "Token tidak ditemukan atau tidak valid.",
						"status" => "invalid_token",
					],
					422
				);
			}

			$user = User::where("email", $validatedData["email"])->first();
			$user->password = Hash::make($validatedData["password"]);
			$user->save();

			DB::table("password_reset_tokens")->where("email", $validatedData["email"])->delete();

			return response()->json(
				[
					"message" => "Password berhasil diubah. Silahkan login dengan password baru Anda.",
				],
				200
			);
		} catch (\Throwable $th) {
			Log::error("Forgot password error: " . $th->getMessage());

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat mereset password. Silakan coba lagi nanti.",
					"error" => app()->environment("local") ? $th->getMessage() : null,
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
