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
			$userId = Auth::id();

			if (!$userId) {
				return response()->json(
					[
						"message" => "Pengguna belum login atau sesi telah berakhir.",
					],
					401
				);
			}

			$user = Cache::store("database")->remember("auth_user_{$userId}", now()->addMinutes(120), function () use ($userId) {
				$user = Auth::user()->loadMissing(["student", "student.homeroomTeacher:id_user,name,email,role"]);
				$user->student->makeHidden(["created_at", "updated_at"]);
				$user->makeHidden(["email_verified_at", "created_at", "updated_at"]);

				return $user;
			});

			return response()->json(["data" => $user]);
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

			Cache::forget("auth_user_{$user->id}");
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
