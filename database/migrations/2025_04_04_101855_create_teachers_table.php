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
		Schema::create("teachers", function (Blueprint $table) {
			$table->bigIncrements("id_teacher");

			$table->unsignedBigInteger("user_id");
			$table->foreign("user_id")->references("id_user")->on("users")->onDelete("cascade")->onUpdate("cascade");

			$table->string("firstname");
			$table->string("lastname")->nullable();
			$table->string("nip")->nullable();
			$table->string("npsn", 8);
			$table->string("school");
			$table->string("birthplace")->nullable();
			$table->date("birthdate")->nullable();
			$table->string("position")->nullable();
			$table->string("contact", 15)->nullable();
			$table->string("avatar")->nullable();
			$table->enum("gender", ["male", "female"])->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("teachers");
	}
};
