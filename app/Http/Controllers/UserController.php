<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
	public function authUser()
	{
		try {
			$user = Auth::user()->loadMissing("teacher");

			$user = Cache::remember("auth_user_{$user->id_user}", now()->addHours(2), function () use ($user) {
				if ($user->role == "peserta_didik") {
					return $user->loadMissing(["student", "student.homeroomTeacher", "student.majorDetail"]);
				} elseif ($user->role == "kepala_program") {
					return $user->loadMissing(["teacher", "majorLed"]);
				}

				return $user->loadMissing(["teacher"]);
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

	public function getAllUsers()
	{
		try {
			$users = User::all();

			$users->loadMissing(["student", "teacher", "student.majorDetail"]);

			return response()->json([
				"data" => $users,
				"message" => "Data seluruh pengguna berhasil didapatkan.",
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

	public function deleteUserById(User $user)
	{
		try {
			if (!$user) {
				return response()->json(
					[
						"message" => "Pengguna tidak ditemukan.",
					],
					404
				);
			}

			$user->forceDelete();

			$users = User::all();

			$users->loadMissing(["student", "teacher", "student.majorDetail"]);

			return response()->json([
				"message" => "Pengguna Berhasil Dihapus.",
				"data" => $users,
			]);
		} catch (\Throwable $e) {
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat menghapus akun. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function removeFromHomeroom(User $user)
	{
		try {
			if (!$user) {
				return response()->json(
					[
						"message" => "Pengguna tidak ditemukan.",
					],
					404
				);
			}

			$user->update([
				"role" => "guru_pembimbing",
			]);

			DB::table("sessions")->where("user_id", $user->id_user)->delete();

			$users = User::all();

			$users->loadMissing(["student", "teacher", "student.majorDetail"]);

			return response()->json([
				"message" => "Status Wali Kelas Berhasil Dihapus.",
				"data" => $users,
			]);
		} catch (\Throwable $e) {
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat menghapus akun. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function promoteToHomeroom(User $user)
	{
		try {
			if (!$user) {
				return response()->json(
					[
						"message" => "Pengguna tidak ditemukan.",
					],
					404
				);
			}

			$user->update([
				"role" => "wali_kelas",
			]);

			DB::table("sessions")->where("user_id", $user->id_user)->delete();

			$users = User::all();

			$users->loadMissing(["student", "teacher", "student.majorDetail"]);

			return response()->json([
				"message" => "Pengguna Berhasil Ditetapkan Sebagai Wali Kelas.",
				"data" => $users,
			]);
		} catch (\Throwable $e) {
			return response()->json(
				[
					"message" => "Terjadi kesalahan saat menghapus akun. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function deleteSelectedUsers(Request $request)
	{
		try {
			$ids = $request->input("ids");

			User::whereIn("id_user", $ids)->forceDelete();

			$users = User::all();

			$users->loadMissing(["student", "teacher", "student.majorDetail"]);

			return response()->json([
				"message" => "Pengguna Yang Dipilih Berhasil Dihapus.",
				"data" => $users,
			]);
		} catch (\Throwable $e) {
			Log::error($e);
			return response()->json(
				[
					"message" => "Gagal menghapus data.",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function promoteToMajorLeader(Request $request, User $user)
	{
		try {
			DB::beginTransaction();

			$currentLeaderId = $request->input("id");
			$majorId = $request->input("majorId");

			if (!$majorId) {
				return response()->json(
					[
						"message" => "ID jurusan tidak ditemukan.",
					],
					400
				);
			}

			if ($currentLeaderId && $currentLeaderId != $user->id_user) {
				$currentLeader = User::find($currentLeaderId);

				if ($currentLeader) {
					$currentLeader->update([
						"role" => "guru_pembimbing",
					]);

					DB::table("sessions")->where("user_id", $currentLeader->id_user)->delete();
				}
			}

			$user->update([
				"role" => "kepala_program",
			]);

			$major = Major::find($majorId);

			if (!$major) {
				DB::rollBack();
				return response()->json(
					[
						"message" => "Jurusan tidak ditemukan.",
					],
					404
				);
			}

			$major->update([
				"major_leader_id" => $user->id_user,
			]);

			DB::table("sessions")->where("user_id", $user->id_user)->delete();

			$users = User::with(["student", "teacher", "student.majorDetail"])->get();

			DB::commit();

			return response()->json([
				"message" => "Pengguna berhasil ditetapkan sebagai Kepala Program.",
				"data" => $users,
			]);
		} catch (\Throwable $e) {
			DB::rollBack();

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat mengubah peran pengguna. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}

	public function promoteToManagement(Request $request, User $user)
	{
		try {
			DB::beginTransaction();

			$currentManagementId = $request->input("id");
			$position = $request->input("position");

			if ($currentManagementId && $currentManagementId != $user->id_user) {
				$currentManagement = User::find($currentManagementId);

				if ($currentManagement) {
					$currentManagement->update([
						"role" => "guru_pembimbing",
					]);

					if ($currentManagement->teacher) {
						$currentManagement->teacher->update([
							"position" => null,
						]);
					}

					DB::table("sessions")->where("user_id", $currentManagement->id_user)->delete();
				}
			}

			$user->update([
				"role" => "manajemen_sekolah",
			]);

			if ($user->teacher) {
				$user->teacher->update([
					"position" => $position,
				]);
			}

			DB::table("sessions")->where("user_id", $user->id_user)->delete();

			$users = User::with(["student", "teacher", "student.majorDetail"])->get();

			DB::commit();

			return response()->json([
				"message" => "Pengguna berhasil ditetapkan sebagai {$position}.",
				"data" => $users,
			]);
		} catch (\Throwable $e) {
			DB::rollBack();

			return response()->json(
				[
					"message" => "Terjadi kesalahan saat mengubah peran pengguna. Silakan coba lagi.",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}
}
