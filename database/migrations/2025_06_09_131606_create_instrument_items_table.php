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
		Schema::create("instrument_items", function (Blueprint $table) {
			$table->bigIncrements("id_instrument_item");

			$table->unsignedBigInteger("instrument_section_id");
			$table->foreign("instrument_section_id")->references("id_instrument_section")->on("instrument_sections")->onDelete("cascade")->onUpdate("cascade");

			$table->string("item_code");
			$table->text("item_label");

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("instrument_items");
	}
};
