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
	 * Get the instrument records that connected with the major
	 *
	 */
	public function instrument()
	{
		return $this->hasOne(Instrument::class, "major_id", "id_major");
	}

	/**
	 * Get the major leader records that connected with the major
	 *
	 */
	public function majorLeader()
	{
		return $this->belongsTo(User::class, "major_leader_id", "id_user");
	}
}
