<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Internship extends Model
{
	protected $primaryKey = "id_internship";

	protected $guarded = ["id_internship"];

	/**
	 * Get the student data that doing the intern
	 *
	 * @return BelongsTo
	 */
	public function student(): BelongsTo
	{
		return $this->belongsTo(User::class, "student_id", "id_user");
	}

	/**
	 * Get the teacher that following the intern
	 *
	 * @return BelongsTo
	 */
	public function teacher(): BelongsTo
	{
		return $this->belongsTo(User::class, "teacher_id", "id_user");
	}
}
