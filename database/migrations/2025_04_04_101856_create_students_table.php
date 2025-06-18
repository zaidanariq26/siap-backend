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
		Schema::create("students", function (Blueprint $table) {
			$table->bigIncrements("id_user");

			$table->unsignedBigInteger("user_id");
			$table->foreign("user_id")->references("id_user")->on("users")->onDelete("cascade")->onUpdate("cascade");

			$table->string("firstname");
			$table->string("lastname")->nullable();
			$table->string("nisn", 10);
			$table->string("npsn", 8)->nullable();
			$table->string("school")->nullable();

			$table->unsignedBigInteger("major_id")->nullable();
			$table->foreign("major_id")->references("id_major")->on("majors")->onDelete("set null")->onUpdate("cascade");

			$table->unsignedBigInteger("homeroom_teacher_id")->nullable();
			$table->foreign("homeroom_teacher_id")->references("id_user")->on("users")->onDelete("set null")->onUpdate("cascade");

			$table->string("birthplace")->nullable();
			$table->date("birthdate")->nullable();
			$table->string("contact", 15)->nullable();
			$table->enum("religion", ["islam", "katolik", "protestan", "hindu", "buddha", "konghucu"])->nullable();
			$table->enum("gender", ["male", "female"])->nullable();
			$table->string("avatar")->nullable();
			$table->string("emergency_contact", 15)->nullable();
			$table->string("emergency_contact_name")->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("students");
	}
};
