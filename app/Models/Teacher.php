<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
	protected $guarded = ["id"];

	protected $primaryKey = "id_teacher";

	/**
	 * Get the user that owns the Teacher
	 *
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, "user_id");
	}

	/**
	 * Get the user associated with the Student as homeroom
	 *
	 */
	public function students(): HasMany
	{
		return $this->hasMany(Student::class, "homeroom_teacher_id", "id_teacher");
	}
}
