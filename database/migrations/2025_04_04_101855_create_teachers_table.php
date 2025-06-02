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
		Schema::create("teachers", function (Blueprint $table) {
			$table->bigIncrements("id_teacher")->primary();

			$table->unsignedBigInteger("user_id");
			$table->foreign("user_id")->references("id_user")->on("users")->onDelete("cascade")->onUpdate("cascade");

			$table->string("firstname")->nullable();
			$table->string("lastname")->nullable();
			$table->string("nip")->unique()->nullable();
			$table->string("npsn")->nullable();
			$table->string("school")->nullable();
			$table->string("birthplace")->nullable();
			$table->date("birthdate")->nullable();
			$table->string("position")->nullable();

			$table->unsignedBigInteger("major_id")->nullable();
			$table->foreign("major_id")->references("id_major")->on("majors")->onDelete("set null")->onUpdate("cascade");

			$table->string("contact")->nullable();
			$table->string("religion")->nullable();
			$table->string("avatar")->nullable();
			$table->enum("gender", ["male", "female"])->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("teachers");
	}
};
