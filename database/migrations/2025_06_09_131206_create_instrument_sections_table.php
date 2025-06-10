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
		Schema::create("instrument_sections", function (Blueprint $table) {
			$table->bigIncrements("id_instrument_section");

			$table->unsignedBigInteger("instrument_id");
			$table->foreign("instrument_id")->references("id_instrument")->on("instruments")->onDelete("cascade")->onUpdate("cascade");

			$table->string("section_code");
			$table->text("section_label");

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("instrument_sections");
	}
};
