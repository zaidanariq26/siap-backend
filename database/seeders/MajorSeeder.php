<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$majors = [
			[
				"code" => "DKV",
				"name" => "Desain Komunikasi Visual",
			],
			[
				"code" => "AK",
				"name" => "Akuntansi",
			],
			[
				"code" => "BR",
				"name" => "Bisnis Retail",
			],
			[
				"code" => "MP",
				"name" => "Manajemen Perkantoran",
			],
		];

		foreach ($majors as $major) {
			Major::create($major);
		}
	}
}
