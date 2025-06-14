<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeacherSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$user = User::create([
			"name" => "Nila Citra",
			"slug" => "nila-citra",
			"email" => "walas@gmail.com",
			"password" => bcrypt("password"),
			"role" => "wali_kelas",
			"email_verified_at" => now(),
		]);

		Teacher::create([
			"user_id" => $user->id_user,
			"firstname" => "Nila",
			"lastname" => "Citra",
			"nip" => 1111111123,
			"school" => "SMKN 16 Jakarta",
			"birthplace" => "jakarta",
			"birthdate" => Carbon::parse("1990-05-10"),
			"contact" => "08282882882",
			"religion" => "islam",
			"gender" => "female",
		]);

		$kpk = User::create([
			"name" => "Amir Nur",
			"slug" => "amir-nur",
			"email" => "kpk@gmail.com",
			"password" => bcrypt("password"),
			"email_verified_at" => now(),
			"role" => "kepala_program",
		]);

		Teacher::create([
			"user_id" => $kpk->id_user,
			"firstname" => "Amir",
			"lastname" => "Nur",
			"nip" => 1111113123,
			"school" => "SMKN 16 Jakarta",
			"birthplace" => "jakarta",
			"birthdate" => Carbon::parse("1990-05-10"),
			"contact" => "08282882882",
			"religion" => "islam",
			"gender" => "male",
			"major_id" => 1,
		]);
	}
}
