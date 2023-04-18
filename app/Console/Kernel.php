<?php

namespace App\Console;

use App\Console\Commands\DailyTraffic;
use App\Models\Link;
use App\Models\Log;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule)
    {
        $schedule->command(DailyTraffic::class)->everyFiveMinutes();
    }
}
