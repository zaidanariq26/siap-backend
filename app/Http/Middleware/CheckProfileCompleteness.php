<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileCompleteness
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		$user = Auth::user();

		if ($user && $user->role === "peserta_didik") {
			$student = $user->student;

			$requiredFields = [
				"npsn",
				"school",
				"major",
				"homeroom_teacher_id",
				"birthplace",
				"birthdate",
				"religion",
				"contact",
				"gender",
				"emergency_contact",
				"emergency_contact_name",
			];

			foreach ($requiredFields as $field) {
				if (empty($student->$field)) {
					return response()->json(
						[
							"status" => "profile_incomplete",
							"message" => "Profil Anda belum lengkap. Silakan lengkapi profil terlebih dahulu.",
						],
						409
					);
				}
			}
		}

		return $next($request);
	}
}
