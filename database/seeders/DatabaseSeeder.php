<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		$this->call([UserSeeder::class, TeacherSeeder::class, StudentSeeder::class]);
	}

	// CREATE TABLE data_students (
	// id INT AUTO_INCREMENT PRIMARY KEY,
	// name VARCHAR(100),
	// npsn VARCHAR(20),
	// gender ENUM('L', 'P'), -- L untuk laki-laki, P untuk perempuan
	// nisn VARCHAR(20),
	// birthplace VARCHAR(100),
	// birthday DATE,
	// class VARCHAR(50),
	// password VARCHAR(255),
	// email VARCHAR(100),
	// );
}
