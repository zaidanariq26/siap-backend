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
		Schema::create("observations", function (Blueprint $table) {
			$table->ulid("id_observation")->primary();

			$table->char("internship_id", 26);
			$table->foreign("internship_id")->references("id_internship")->on("internships")->onDelete("cascade")->onUpdate("cascade");

			$table->string("name");
			$table->string("status");
			$table->json("section_score")->nullable();
			$table->string("total_score")->nullable();
			$table->string("note")->nullable();
			$table->date("date")->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("observations");
	}
};
