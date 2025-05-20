<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
	protected $primaryKey = "id_attendance";

	protected $guarded = ["id_attendance"];

	/**
	 * Get student records that associated with attendance
	 *
	 * @return BelongsTo
	 */
	public function student(): BelongsTo
	{
		return $this->belongsTo(User::class, "student_id", "id_user");
	}

	/**
	 * Get internship records that associated with attendance
	 *
	 * @return BelongsTo
	 */
	public function internship(): BelongsTo
	{
		return $this->belongsTo(Internship::class, "internship_id", "id_internship");
	}
}
