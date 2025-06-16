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
		Schema::create("internships", function (Blueprint $table) {
			$table->ulid("id_internship")->unique();

			$table->unsignedBigInteger("student_id");
			$table->foreign("student_id")->references("id_user")->on("users")->onDelete("cascade")->onUpdate("cascade");

			$table->unsignedBigInteger("teacher_id")->nullable();
			$table->foreign("teacher_id")->references("id_user")->on("users")->onDelete("set null")->onUpdate("cascade");

			$table->string("job_name");
			$table->string("company_name");
			$table->string("instructor_name");
			$table->string("instructor_contact", 15);
			$table->string("teacher_contact", 15);
			$table->date("start_date");
			$table->date("end_date");
			$table->enum("status", ["pending", "ongoing", "completed"])->default("pending");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("internships");
	}
};
