<?php

namespace App\Http\Controllers;

use App\Helpers\SlugHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\EditProfileStudentRequest;
use App\Http\Requests\EditProfileTeacherRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
	public function editProfileStudent(EditProfileStudentRequest $request)
	{
		$validatedData = $request->validated();

		try {
			DB::beginTransaction();

			$user = Auth::user();

			$fullName = $validatedData["firstname"];
			if (!empty($validatedData["lastname"])) {
				$fullName .= " " . $validatedData["lastname"];
			}

			$slug = $user->slug;
			if ($user->name != $fullName) {
				$slug = SlugHelper::generateUniqueSlug(User::class, $fullName, "slug", $user->id_user);
			}

			$user->update([
				"email" => $validatedData["email"],
				"name" => $fullName,
				"slug" => $slug,
			]);

			$user->student()->update([
				"firstname" => $validatedData["firstname"],
				"lastname" => $validatedData["lastname"] ?? "",
				"school" => $validatedData["school"],
				"nisn" => $validatedData["nisn"],
				"major_id" => $validatedData["major_id"],
				"homeroom_teacher_id" => $validatedData["homeroom_teacher_id"],
				"birthplace" => $validatedData["birthplace"],
				"birthdate" => $validatedData["birthdate"],
				"contact" => $validatedData["contact"],
				"religion" => $validatedData["religion"],
				"gender" => $validatedData["gender"],
				"emergency_contact" => $validatedData["emergency_contact"],
				"emergency_contact_name" => $validatedData["emergency_contact_name"],
			]);

			DB::commit();

			Cache::forget("auth_user_{$user->id_user}");

			$user = Cache::store("database")->remember("auth_user_{$user->id_user}", now()->addMinutes(120), function () use ($user) {
				$user->loadMissing(["student", "student.homeroomTeacher", "student.majorDetail"]);
				return $user;
			});

			return response()->json([
				"code" => 200,
				"message" => "Profil Berhasil Diperbarui",
				"data" => $user,
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(
				[
					"code" => 500,
					"message" => "Terjadi kesalahan saat memperbarui data. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}
	public function editProfileTeacher(EditProfileTeacherRequest $request)
	{
		$validatedData = $request->validated();

		try {
			DB::beginTransaction();

			$user = Auth::user();
			$userId = $user->id_user;

			$fullName = $validatedData["firstname"];
			if (!empty($validatedData["lastname"])) {
				$fullName .= " " . $validatedData["lastname"];
			}

			$user->update([
				"email" => $validatedData["email"],
				"name" => $fullName,
			]);

			$user->teacher()->update([
				"school" => $validatedData["school"],
				"nip" => $validatedData["nip"],
				"birthplace" => $validatedData["birthplace"],
				"birthdate" => $validatedData["birthdate"],
				"contact" => $validatedData["contact"],
				"religion" => $validatedData["religion"],
				"gender" => $validatedData["gender"],
			]);

			DB::commit();

			Cache::forget("auth_user_{$userId}");

			$user = Cache::store("database")->remember("auth_user_{$userId}", now()->addMinutes(120), function () use ($user) {
				$user->loadMissing(["teacher"]);
				return $user;
			});

			return response()->json([
				"message" => "Profil Berhasil Diperbarui",
				"data" => $user,
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json(
				[
					"code" => 500,
					"message" => "Terjadi kesalahan saat memperbarui data. Silakan coba lagi!",
					"error" => app()->environment("local") ? $e->getMessage() : null,
				],
				500
			);
		}
	}
}
