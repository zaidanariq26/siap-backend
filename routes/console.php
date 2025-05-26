<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command("internship:update-status")->dailyAt("00:01")->withoutOverlapping();
Schedule::command("attendance:generate-missing")->dailyAt("03:00")->withoutOverlapping();
