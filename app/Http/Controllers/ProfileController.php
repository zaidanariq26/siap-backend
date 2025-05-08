<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\EditProfileStudentRequest;

class ProfileController extends Controller
{
	public function editProfileStudent(EditProfileStudentRequest $request)
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

			$user->student()->update([
				"school" => $validatedData["school"],
				"nisn" => $validatedData["nisn"],
				"major" => $validatedData["major"],
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

			Cache::forget("auth_user_{$userId}");

			$user = Cache::store("database")->remember("auth_user_{$userId}", now()->addMinutes(120), function () use ($user) {
				$user->loadMissing(["student", "student.homeroomTeacher"]);
				$user->student->makeHidden(["created_at", "updated_at"]);
				$user->makeHidden(["email_verified_at", "created_at", "updated_at"]);
				return $user;
			});

			return response()->json([
				"code" => 200,
				"message" => "Profil berhasil diperbarui.",
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
