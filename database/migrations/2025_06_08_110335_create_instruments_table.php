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
		Schema::create("instruments", function (Blueprint $table) {
			$table->ulid("id_instrument")->primary();

			$table->unsignedBigInteger("teacher_id")->nullable();
			$table->foreign("teacher_id")->references("id_user")->on("users")->onDelete("set null")->onUpdate("cascade");

			$table->unsignedBigInteger("major_id")->unique();
			$table->foreign("major_id")->references("id_major")->on("majors")->onDelete("cascade")->onUpdate("cascade");

			$table->enum("status", ["applied", "not_applied"])->default("not_applied");
			$table->string("academic_year");

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("instruments");
	}
};
