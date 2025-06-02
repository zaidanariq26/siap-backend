<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Attendance extends Model
{
	use HasUlids;

	protected $primaryKey = "id_attendance";
	protected $guarded = ["id_attendance"];
	public $incrementing = false;
	protected $keyType = "string";

	/**
	 * Get the student user associated with this attendance record.
	 *
	 * @return BelongsTo
	 */
	public function student(): BelongsTo
	{
		return $this->belongsTo(User::class, "student_id", "id_user");
	}

	/**
	 * Get the supervised teacher user associated with this attendance record.
	 *
	 * @return BelongsTo
	 */
	public function teacher(): BelongsTo
	{
		return $this->belongsTo(User::class, "teacher_id", "id_user");
	}

	/**
	 * Get the internship associated with this attendance record.
	 *
	 * @return BelongsTo
	 */
	public function internship(): BelongsTo
	{
		return $this->belongsTo(Internship::class, "internship_id", "id_internship");
	}

	/**
	 * Get the journal record associated with this attendance.
	 *
	 * @return HasOne
	 */
	public function journal(): HasOne
	{
		return $this->hasOne(Journal::class, "attendance_id", "id_attendance");
	}
}
