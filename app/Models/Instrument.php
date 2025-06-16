<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instrument extends Model
{
	use HasUlids;
	protected $primaryKey = "id_instrument";
	protected $guarded = ["id_instrument"];
	public $incrementing = false;
	protected $keyType = "string";

	/**
	 * Get the instrument records that connected with the major
	 *
	 */
	public function majorDetail()
	{
		return $this->belongsTo(Major::class, "major_id");
	}

	/**
	 * Get the instrument section records that connected with this instrument
	 *
	 */
	public function instrumentSections()
	{
		return $this->hasMany(InstrumentSection::class, "instrument_id", "id_instrument");
	}
}
