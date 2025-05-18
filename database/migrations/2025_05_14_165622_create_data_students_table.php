<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create("data_students", function (Blueprint $table) {
			$table->string("id");
			$table->string("name", 100);
			$table->string("npsn", 20);
			$table->enum("gender", ["L", "P"]); // L: Laki-laki, P: Perempuan
			$table->string("nisn", 20);
			$table->string("birthplace", 100);
			$table->date("birthdate");
			$table->string("class", 50);
			$table->string("password");
			$table->string("email", 100)->unique(); // biasanya dibuat unik
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("data_students");
	}
};
