<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Internship extends Model
{
	use HasUlids;

	protected $primaryKey = "id_internship";
	protected $guarded = ["id_internship"];
	public $incrementing = false;
	protected $keyType = "string";

	/**
	 * Get the user who is a student and doing this internship.
	 *
	 * @return BelongsTo
	 */
	public function student(): BelongsTo
	{
		return $this->belongsTo(User::class, "student_id", "id_user");
	}

	/**
	 * Get the teacher that supervises this internship
	 *
	 * @return BelongsTo
	 */
	public function teacher(): BelongsTo
	{
		return $this->belongsTo(User::class, "teacher_id", "id_user");
	}

	/**
	 * Get all attendances connected to this internship.
	 *
	 * @return HasMany
	 */
	public function attendances(): HasMany
	{
		return $this->hasMany(Attendance::class, "internship_id", "id_internship");
	}

	/**
	 * Get all journals connected to this internship.
	 *
	 * @return HasMany
	 */
	public function journals(): HasMany
	{
		return $this->hasMany(Journal::class, "internship_id", "id_internship");
	}

	/**
	 * Get all journals connected to this internship.
	 *
	 * @return HasMany
	 */
	public function assesments(): HasMany
	{
		return $this->hasMany(Assesment::class, "internship_id", "id_internship");
	}
}
