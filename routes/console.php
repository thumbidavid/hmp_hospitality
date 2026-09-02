<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('youtube:check-live')->everyMinute();
