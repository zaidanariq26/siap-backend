<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
	return view("welcome");
});

require __DIR__ . "/auth.php";
