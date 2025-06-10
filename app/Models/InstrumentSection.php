<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstrumentSection extends Model
{
	protected $primaryKey = "id_instrument_section";
	protected $guarded = ["id_instrument_item"];

	/**
	 * Get the instrument item records that connected with this instrument section
	 *
	 */
	public function instrumentItems(): HasMany
	{
		return $this->hasMany(InstrumentItem::class, "instrument_section_id", "id_instrument_section");
	}
}
