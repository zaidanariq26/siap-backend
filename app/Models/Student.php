<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
	protected $guarded = ["id_student"];

	protected $primaryKey = "id_student";

	/**
	 * Get the user associated with this student data.
	 *
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, "user_id");
	}

	/**
	 * Get the teacher that owns the Student as a homeroom.
	 *
	 */
	public function homeroomTeacher(): BelongsTo
	{
		return $this->belongsTo(User::class, "homeroom_teacher_id");
	}
}
