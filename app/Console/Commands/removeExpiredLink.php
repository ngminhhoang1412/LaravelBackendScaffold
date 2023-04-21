<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\Link;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class removeExpiredLink extends Command
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
     * @return int
     */
    public function handle()
    {
        
        DB::beginTransaction();
        try{
            // Get all expired link
            $expiredLink = DB::table(Link::retrieveTableName())
                            ->whereDate('created_at','<=',now()->subYear())
                            ->get('id');
            $ids = array();
            // Put it in an array 
            // Because (DB::table->get() return an an array with objects has an id)
            for ($i=0; $i < count($expiredLink); $i++) { 
                array_push($ids, $expiredLink[$i]->id);
            }
            
            // Delete all related log first
            DB::table(Log::retrieveTableName())
                ->whereIn('id', $ids)
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
