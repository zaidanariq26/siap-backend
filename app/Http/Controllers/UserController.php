<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
	public function authUser()
	{
		try {
			$user = Auth::user()->loadMissing("teacher");

			$user = Cache::remember("auth_user_{$user->id_user}", now()->addHours(2), function () use ($user) {
				if ($user->role === "peserta_didik") {
					return $user->loadMissing(["student", "student.homeroomTeacher", "student.majorDetail"]);
				}

				return $user->loadMissing(["teacher", "teacher.majorDetail"]);
			});

			return response()->json([
				"data" => $user,
				"message" => "Data pengguna berhasil didapatkan.",
			]);
		} catch (\Throwable $e) {
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat mengambil data pengguna.",
					"error" => $e->getMessage(),
				],
				500
			);
		}
	}

	public function deleteUser(Request $request)
	{
		try {
			$user = Auth::user();

			Cache::forget("auth_user_{$user->id_user}");
			Auth::guard("web")->logout();
			$request->session()->invalidate();
			$request->session()->regenerateToken();

			$user->delete();

			return response()->json([
				"code" => 200,
				"message" => "Akun berhasil dihapus.",
			]);
		} catch (\Throwable $e) {
			return response()->json(
				[
					"code" => 500,
					"message" => "Terjadi kesalahan saat menghapus akun. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}
}
