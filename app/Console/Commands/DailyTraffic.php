<?php

namespace App\Console\Commands;

use App\Models\Link;
use App\Models\Log;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyTraffic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'link:takeAmount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Log traffic amount by date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            Link::query()->get()
                ->map(function($item) {
                    DB::table(Log::retrieveTableName())->Insert([
                        'date' => date('Y-m-d'),
                        'link_id' => $item->id,
                        'amount' => $item->amount,
                    ]);
                    DB::table(Link::retrieveTableName())->update([
                        'amount' => 0
                    ]);
                });
            DB::commit();
        } catch (Exception $e) {
            echo $e->getMessage();
            DB::rollBack();
        }
    }
}
