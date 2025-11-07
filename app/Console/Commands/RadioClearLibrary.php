<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Track;
use App\Models\PlaylistItem;

class RadioClearLibrary extends Command
{
    protected $signature = 'radio:clear-library';
    protected $description = 'Delete all tracks and related playlist items';

    public function handle()
    {
        $this->info('Clearing library (using TRUNCATE with FK checks off)…');

        DB::beginTransaction();
        try {
            // Turn off FK checks (MySQL/MariaDB)
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Truncate children first, then parents
            PlaylistItem::truncate();
            Track::truncate();

            // (Optional) reset AUTO_INCREMENT explicitly
            DB::statement('ALTER TABLE playlist_items AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE tracks AUTO_INCREMENT = 1');

            // Re-enable FK checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            DB::commit();
            $this->info('Library cleared.');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            // Make *sure* FK checks are back on even if we failed mid-way
            try { DB::statement('SET FOREIGN_KEY_CHECKS=1'); } catch (\Throwable $ignored) {}
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}