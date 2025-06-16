<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Assesment extends Model
{
	use HasUlids;
	protected $primaryKey = "id_assesment";
	protected $guarded = ["id_assesment"];
	public $incrementing = false;
	protected $keyType = "string";
}
