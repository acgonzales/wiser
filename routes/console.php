<?php

use App\Console\Commands\UciScan;
use Illuminate\Support\Facades\Schedule;

Schedule::command(UciScan::class)
    ->everyTenSeconds()
    ->sendOutputTo("./storage/logs/scheduler-logs.txt");
