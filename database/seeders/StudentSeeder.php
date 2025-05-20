<?php

namespace Database\Seeders;

use App\Models\Student;
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
		Student::create([
			"user_id" => 5,
			"npsn" => "20100159",
			"firstname" => "Halimah",
			"lastname" => "Adnan",
			"nisn" => 99999999,
			"school" => "SMKN 16 Jakarta",
			"major" => "dkv",
			"homeroom_teacher_id" => 3,
			"birthplace" => "jakarta",
			"birthdate" => Carbon::parse("2007-05-10"),
			"contact" => "08282882882",
			"religion" => "islam",
			"gender" => "female",
			"emergency_contact" => "089898989",
			"emergency_contact_name" => "Afifah",
		]);
	}
}
