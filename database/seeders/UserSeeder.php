<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// Teacher
		User::create([
			"name" => "Musa Ali",
			"slug" => "musa-ali",
			"email" => "kepsek@gmail.com",
			"password" => bcrypt("password"),
			"email_verified_at" => now(),
			"role" => "manajemen_sekolah",
		]);

		User::create([
			"name" => "Afifah Irfan",
			"slug" => "afifah-irfan",
			"email" => "gupem@gmail.com",
			"password" => bcrypt("password"),
			"email_verified_at" => now(),
			"role" => "guru_pembimbing",
		]);
	}
}
