<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Internship;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateInternshipStatus extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = "internship:update-status";

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Update internship status to ongoing if start date is today";

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$today = Carbon::today()->toDateString();

		$updated = Internship::where("start_date", $today)
			->where("status", "=", "pending")
			->update(["status" => "ongoing"]);

		Log::info("Internship has been updated");

		$this->info("Updated $updated internship(s) to ongoing status.");
	}
}
