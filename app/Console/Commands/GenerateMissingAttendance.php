<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Internship;
use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateMissingAttendance extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = "attendance:generate-missing";

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Generate automatic attendance for students who missed today's attendance.";

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$today = Carbon::today();
		$dayOfWeek = $today->dayOfWeekIso;
		// $dayOfWeek = 7;

		$status = in_array($dayOfWeek, [6, 7]) ? "off" : "no_description";

		$internships = Internship::where("status", "ongoing")->get();

		$created = 0;
		$studentIds = [];

		DB::beginTransaction();

		try {
			foreach ($internships as $internship) {
				$studentId = $internship->student_id;

				if (!$studentId) {
					continue;
				}

				$alreadyAttended = Attendance::where("student_id", $studentId)->whereDate("date", $today)->exists();

				if (!$alreadyAttended) {
					$attendanceData = [
						"student_id" => $studentId,
						"teacher_id" => $internship->teacher_id,
						"internship_id" => $internship->id_internship,

						"date" => $today,
						"status" => $status,
					];

					if ($status == "no_description") {
						$attendanceData["expired_at"] = $today->copy()->addDays(2);
					}

					$attendance = Attendance::create($attendanceData);

					if ($attendance->status != "off") {
						Journal::create([
							"student_id" => $attendance->student_id,
							"teacher_id" => $attendance->teacher_id,
							"internship_id" => $attendance->internship_id,
							"attendance_id" => $attendance->id_attendance,
							"status" => "not_present",
							"date" => $attendance->date,
						]);
					}

					$studentIds[] = $studentId;
					$created++;
				}
			}

			DB::commit();

			$this->info("Automatically created $created attendance records for missing entries.");

			Log::info("Auto-generated attendance created", [
				"total" => $created,
				"date" => $today->toDateString(),
				"student_ids" => $studentIds,
			]);
		} catch (\Exception $e) {
			DB::rollBack();

			$this->error("Failed to generate missing attendance: " . $e->getMessage());

			Log::error("Auto-attendance generation failed", [
				"message" => $e->getMessage(),
				"date" => $today->toDateString(),
			]);
		}
	}
}
