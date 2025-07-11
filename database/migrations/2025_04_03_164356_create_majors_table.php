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
		Schema::create("majors", function (Blueprint $table) {
			$table->bigIncrements("id_major")->primary();

			$table->unsignedBigInteger("major_leader_id")->nullable();
			$table->foreign("major_leader_id")->references("id_user")->on("users")->onDelete("set null")->onUpdate("cascade");

			$table->string("code")->unique();
			$table->string("name");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("majors");
	}
};
