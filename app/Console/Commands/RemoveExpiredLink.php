<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\Link;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveExpiredLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'link:removeExpired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all the expired link';

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
    public function handle(): void
    {
        DB::beginTransaction();
        try{
            $ids = array();
            // Get all expired link
            DB::table(Link::retrieveTableName())
                            ->whereDate('created_at','<=',now()->subYear())
                            ->get('id')->map(function ($value) use (&$ids) {
                                $ids[] = $value->id;
                            });

            // Delete all related log first
            DB::table(Log::retrieveTableName())
                ->whereIn('link_id', $ids)
                ->delete();
            // Delete the expired link
            DB::table(Link::retrieveTableName())
                ->whereIn('id', $ids)
                ->delete();

            DB::commit();
        }
        catch(Exception $e)
        {
            DB::rollback();
        }
    }
}
