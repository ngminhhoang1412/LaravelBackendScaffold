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
    protected $signature = 'link:amount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Log each amount traffic by date';

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
                    $this->addLog($item['link'], $item['amount']);
//                    DB::table(Log::retrieveTableName())->Insert([
//                        'day' => date('Y-m-d'),
//                    ]);
//                    return [
//                        $item['id'] => [
//                            'id' => Str::uuid(),
//                            'day' => date('Y-m-d'),
//                            'link' => $item['link'],
//                            'amount' => $item['amount']
//                        ]
//                    ];
                });
//            $currentRecord = Log::query()
//                ->where([
//                    'title' => $todayDate,
//                    'category' => Log::LOG_CATEGORY_ENUM[3]
//                ])
//                ->first();
//            if ($currentRecord) {
//                $ratings = collect(json_decode($currentRecord['message']));
//                $newJson = $ratings->map(function ($item) {
//                    $item[] = 2;
//                    return $item;
//                });
//                $json = json_encode((object) $newJson);
//            } else {
//                $json = json_encode($link);
//            }

//            DB::table(Log::retrieveTableName())->Insert([
//                'day' => date('Y-m-d'),
//            ], [
//                'message' => $json
//                'message' => 'test'
//            ]);
            DB::commit();
        } catch (Exception $e) {
            echo $e->getMessage();
            DB::rollBack();
        }
    }

    private function addLog($link, $amount)
    {
        $log = new Log();
        $log['day'] = date('Y-m-d');
        $log['link'] = $link;
        $log['amount'] = $amount;
        $log->save();
    }
}
