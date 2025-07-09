<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$user = User::create([
			"name" => "Halimah Adnan",
			"slug" => "halimah-adnan",
			"email" => "sdppia@gmail.com",
			"password" => bcrypt("password"),
			"email_verified_at" => now(),
			"role" => "peserta_didik",
		]);

		Student::create([
			"user_id" => $user->id_user,
			"npsn" => "20100159",
			"firstname" => "Halimah",
			"lastname" => "Adnan",
			"nisn" => 9999999999,
			"school" => "SMKN 16 Jakarta",
			"birthplace" => "jakarta",
			"birthdate" => Carbon::parse("2007-05-10"),
			"contact" => "08282882882",
			"gender" => "female",
			"emergency_contact" => "089898989",
			"emergency_contact_name" => "Afifah",
		]);
	}
}
