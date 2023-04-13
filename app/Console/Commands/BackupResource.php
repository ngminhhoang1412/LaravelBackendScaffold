<?php

namespace App\Console\Commands;

use App\Models\BaseModel;
use App\Models\Worker;
use Illuminate\Console\Command;

class BackupResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backing up resources (including emails and proxies)';

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
    public function handle(): int
    {
        $this->exportCSV(Worker::class);
        return 0;
    }

    /**
     * @param $tableName
     * @return void
     */
    private function exportCSV($tableName)
    {
        // TODO: will come back later
        /** @var BaseModel $tableClass */
        $tableClass = new $tableName;
        $records = $tableClass::query()
            ->get();
//        echo "backing up" . json_encode($records);
        $backupFileName = sprintf('resource-%s.csv', date('d-m-Y_H:i:s'));
        $backupFilePath = storage_path() . "/resource/backup/" . $backupFileName;
        $file = fopen($backupFilePath, 'w');
        fwrite($file, $records);
        fclose($file);
//        if ($records) {
//            ob_start();
//            $df = fopen("php://output", 'w');
//            fputcsv($df, array_keys(reset($records)));
//            foreach ($array as $row) {
//                fputcsv($df, $row);
//            }
//            fclose($df);
//            return ob_get_clean();
//        }
    }
}
