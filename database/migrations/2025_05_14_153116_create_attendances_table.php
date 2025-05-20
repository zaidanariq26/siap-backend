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
		Schema::create("attendances", function (Blueprint $table) {
			$table->bigIncrements("id_attendance");
			$table->unsignedBigInteger("student_id");
			$table->foreign("student_id")->references("id_user")->on("users")->onDelete("cascade")->onUpdate("cascade");
			$table->unsignedBigInteger("internship_id");
			$table->foreign("internship_id")->references("id_internship")->on("internships")->onDelete("cascade")->onUpdate("cascade");

			$table->enum("status", ["present", "excused", "sick", "no_description", "off"]);
			$table->string("note")->nullable();
			$table->date("date");
			$table->time("time")->nullable();
			$table->decimal("latitude", 10, 8)->nullable();
			$table->decimal("longitude", 11, 8)->nullable();
			$table->timestamp("expired_at")->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("attendances");
	}
};
