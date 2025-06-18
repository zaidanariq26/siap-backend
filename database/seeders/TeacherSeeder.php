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
			"email" => "nila_citra@gmail.com",
			"password" => bcrypt("password"),
			"role" => "manajemen_sekolah",
			"email_verified_at" => now(),
		]);

		Teacher::create([
			"user_id" => $user->id_user,
			"firstname" => "Nila",
			"lastname" => "Citra",
			"nip" => 1111111123,
			"npsn" => "20100159",
			"school" => "SMKN 16 Jakarta",
			"birthplace" => "jakarta",
			"birthdate" => Carbon::parse("1990-05-10"),
			"position" => "Kepala Sekolah",
			"contact" => "08282882882",
			"religion" => "islam",
			"gender" => "female",
		]);

		$kpk = User::create([
			"name" => "Amir Nur",
			"slug" => "amir-nur",
			"email" => "amir_nur@gmail.com",
			"password" => bcrypt("password"),
			"email_verified_at" => now(),
			"role" => "wali_kelas",
		]);

		Teacher::create([
			"user_id" => $kpk->id_user,
			"firstname" => "Amir",
			"lastname" => "Nur",
			"nip" => 1111113123,
			"npsn" => "20100159",
			"school" => "SMKN 16 Jakarta",
			"birthplace" => "jakarta",
			"birthdate" => Carbon::parse("1990-05-10"),
			"contact" => "08282882882",
			"religion" => "islam",
			"gender" => "male",
		]);
	}
}
