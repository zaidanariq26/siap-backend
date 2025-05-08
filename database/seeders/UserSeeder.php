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
			"email" => "kepsek@gmail.com",
			"password" => bcrypt("password"),
			"email_verified_at" => now(),
			"role" => "manajemen_sekolah",
		]);
		User::create([
			"name" => "Amir Nur",
			"email" => "kpk@gmail.com",
			"password" => bcrypt("password"),
			"email_verified_at" => now(),
			"role" => "kepala_program",
		]);
		User::create([
			"name" => "Nila Citra",
			"email" => "walas@gmail.com",
			"password" => bcrypt("password"),
			"role" => "wali_kelas",

			"email_verified_at" => now(),
		]);
		User::create([
			"name" => "Afifah Irfan",
			"email" => "gupem@gmail.com",
			"password" => bcrypt("password"),
			"email_verified_at" => now(),
			"role" => "guru_pembimbing",
		]);

		// Student
		User::create([
			"name" => "Halimah Adnan",
			"email" => "halimah_adnan@gmail.com",
			"password" => bcrypt("password"),
			"email_verified_at" => now(),
			"role" => "peserta_didik",
		]);
		// User::create([
		// 	"name" => "Ahmad Fikri",
		// 	"email" => "ahmad_fikri@gmail.com",
		// 	"password" => bcrypt("password"),
		// 	"email_verified_at" => now(),
		// ]);
		// User::create([
		// 	"name" => "Muhammad Fatimah",
		// 	"email" => "muhammad_fatimah@gmail.com",
		// 	"password" => bcrypt("password"),
		// 	"email_verified_at" => now(),
		// ]);
		// User::create([
		// 	"name" => "Wahyu Cahya",
		// 	"email" => "wahyu_cahya@gmail.com",
		// 	"password" => bcrypt("password"),
		// 	"email_verified_at" => now(),
		// ]);
		// User::create([
		// 	"name" => "Ahmad Arif",
		// 	"email" => "ahmad_arif@gmail.com",
		// 	"password" => bcrypt("password"),
		// 	"email_verified_at" => now(),
		// ]);
	}
}
