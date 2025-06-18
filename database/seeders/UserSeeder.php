<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// // Teacher
		// User::create([
		// 	"name" => "Musa Ali",
		// 	"slug" => "musa-ali",
		// 	"email" => "kepsek@gmail.com",
		// 	"password" => bcrypt("password"),
		// 	"email_verified_at" => now(),
		// 	"role" => "manajemen_sekolah",
		// ]);

		// User::create([
		// 	"name" => "Afifah Irfan",
		// 	"slug" => "afifah-irfan",
		// 	"email" => "gupem@gmail.com",
		// 	"password" => bcrypt("password"),
		// 	"email_verified_at" => now(),
		// 	"role" => "guru_pembimbing",
		// ]);

		// $names = [
		// 	"Rizky Ramadhan",
		// 	"Alya Putri",
		// 	"Fauzan Hidayat",
		// 	"Nadia Zahra",
		// 	"Iqbal Maulana",
		// 	"Salsabila Aulia",
		// 	"Dimas Aditya",
		// 	"Kirana Lestari",
		// 	"Zaki Alfarizi",
		// 	"Citra Ayuningtyas",
		// ];

		// foreach ($names as $index => $name) {
		// 	User::create([
		// 		"name" => $name,
		// 		"slug" => Str::slug($name),
		// 		"email" => "peserta" . ($index + 1) . "@gmail.com",
		// 		"password" => bcrypt("password"),
		// 		"email_verified_at" => now(),
		// 		"role" => "guru_pembimbing",
		// 	]);
		// }
	}
}
