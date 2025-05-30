<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeacherSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		Teacher::create([
			"user_id" => 3,
			"firstname" => "Nila",
			"lastname" => "Citra",
			"nip" => 1111111123,
			"school" => "SMKN 16 Jakarta",
			"position" => "Wali Kelas",
			"birthplace" => "jakarta",
			"birthdate" => Carbon::parse("1990-05-10"),
			"contact" => "08282882882",
			"religion" => "islam",
			"gender" => "female",
		]);
	}
}
