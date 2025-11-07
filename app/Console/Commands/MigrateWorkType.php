<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateWorkType extends Command
{
    protected $signature = 'migrate:worktype';
    protected $description = 'Migrate work_type from tbl_campus_data to users';

    public function handle()
    {
        $this->info('Migrating work_type from tbl_campus_data to users...');

        DB::table('tbl_campus_data')
            ->orderBy('id') // Required for chunking
            ->chunk(100, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('users')
                        ->where('id', $row->reference)
                        ->update(['work_type' => $row->work_type]);
                }
            });

        $this->info('Migration complete!');
    }
}
