<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Observation extends Model
{
	use HasUlids;

	protected $primaryKey = "id_observation";
	protected $guarded = ["id_observation"];
	public $incrementing = false;
	protected $keyType = "string";
}
