<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
	protected $guarded = ["id_teacher"];

	protected $primaryKey = "id_teacher";

	/**
	 * Get the user associated with this teacher data.
	 *
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, "user_id");
	}
}
