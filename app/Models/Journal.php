<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
	use HasUlids;

	protected $guarded = ["id_journal"];
	protected $primaryKey = "id_journal";
	public $incrementing = false;
	protected $keyType = "string";

	/**
	 * Get the user record who is the student associated with this journal
	 *
	 * @return BelongsTo
	 */
	public function student(): BelongsTo
	{
		return $this->belongsTo(User::class, "student_id", "id_user");
	}

	/**
	 * Get the user record who is the supervised teacher associated with this journal
	 *
	 * @return BelongsTo
	 */
	public function teacher(): BelongsTo
	{
		return $this->belongsTo(User::class, "teacher_id", "id_user");
	}

	/**
	 * Get attendance record that associated with journal
	 *
	 * @return BelongsTo
	 */
	public function attendance(): BelongsTo
	{
		return $this->belongsTo(Attendance::class, "attendance_id", "id_attendance");
	}

	/**
	 * Get internship record that associated with journal
	 *
	 * @return BelongsTo
	 */
	public function internship(): BelongsTo
	{
		return $this->belongsTo(Internship::class, "internship_id", "id_internship");
	}
}
