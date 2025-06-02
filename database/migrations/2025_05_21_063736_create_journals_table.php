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
		Schema::create("journals", function (Blueprint $table) {
			$table->ulid("id_journal")->primary();

			$table->unsignedBigInteger("student_id");
			$table->foreign("student_id")->references("id_user")->on("users")->onDelete("cascade")->onUpdate("cascade");

			$table->unsignedBigInteger("teacher_id")->nullable();
			$table->foreign("teacher_id")->references("id_user")->on("users")->onDelete("set null")->onUpdate("cascade");

			$table->char("internship_id", 26);
			$table->foreign("internship_id")->references("id_internship")->on("internships")->onDelete("cascade")->onUpdate("cascade");

			$table->char("attendance_id", 26);
			$table->foreign("attendance_id")->references("id_attendance")->on("attendances")->onDelete("cascade")->onUpdate("cascade");

			$table->enum("status", ["not_created", "in_review", "needs_revision", "approved", "not_present"])->default("not_created");
			$table->date("date");
			$table->string("description")->nullable();
			$table->string("image_path")->nullable();
			$table->string("captured_at")->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("journals");
	}
};
