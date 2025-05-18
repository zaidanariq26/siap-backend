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
			$table->time("time");
			$table->decimal("latitude", 10, 8);
			$table->decimal("longitude", 11, 8);
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
