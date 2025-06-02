<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SlugHelper
{
	public static function generateUniqueSlug(string $modelClass, string $name, string $column = "slug", $ignoreId = null): string
	{
		$slug = Str::slug($name);
		$originalSlug = $slug;
		$count = 1;

		$query = $modelClass::where($column, $slug);
		if ($ignoreId) {
			$query->where("id_user", "!=", $ignoreId);
		}

		while ($query->exists()) {
			$slug = $originalSlug . "-" . $count++;
			$query = $modelClass::where($column, $slug);
			if ($ignoreId) {
				$query->where("id_user", "!=", $ignoreId);
			}
		}

		Log::info($slug);
		return $slug;
	}
}
