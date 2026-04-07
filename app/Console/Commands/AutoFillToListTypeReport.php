<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoFillToListTypeReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoFillToListTypeReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $list_type = [
            'protein_level',
            'salt_level',
            'histamine_level',
            'acid_level',
            'amon_level',
            'color',
        ];
        $data_insert = [];
        foreach($list_type as $name) {
            $data_insert = [];
            foreach(range(5, 70) as $number) {
                $data_insert[] = [
                    'name' => $number,
                    'type_report' => $name,
                    'created_at' => date("Y-m-d H:i:s"),
                    'create_user_id' => 1,
                ];
            }
            DB::table('list_type_report')->insert($data_insert);
        }
    }
}
