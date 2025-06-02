<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
	protected $guarded = ["id_major"];
	protected $primaryKey = "id_major";

	/**
	 * Get the student records that owned the major
	 *
	 */
	public function students(): HasMany
	{
		return $this->hasMany(Student::class, "major_id", "id_major");
	}

	/**
	 * Get the teacher records that owned the major
	 *
	 */
	public function teachers(): HasMany
	{
		return $this->hasMany(Teacher::class, "major_id", "id_major");
	}
}
