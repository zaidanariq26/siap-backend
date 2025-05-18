<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
	protected $primaryKey = "id_attendance";

	protected $guarded = ["id_attendance"];
}
