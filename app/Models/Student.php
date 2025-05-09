<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
	protected $guarded = ["id"];

	protected $primaryKey = "id_student";

	/**
	 * Get the user that owns the Student
	 *
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, "user_id");
	}

	/**
	 * Get the teacher that owns the Student as a homeroom
	 *
	 */
	public function homeroomTeacher(): BelongsTo
	{
		return $this->belongsTo(User::class, "homeroom_teacher_id");
	}
}
