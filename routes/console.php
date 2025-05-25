<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

// Artisan::command("inspire", function () {
// 	$this->comment(Inspiring::quote());
// })->purpose("Display an inspiring quote");

Schedule::command("internship:update-status")->dailyAt("00:01")->withoutOverlapping();
Schedule::command("attendance:generate-missing")->dailyAt("09:53")->withoutOverlapping();
