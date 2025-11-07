<?php
// app/Console/Commands/RadioNormalizeTracks.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Track;

class RadioNormalizeTracks extends Command
{
    protected $signature = 'radio:normalize-tracks';
    protected $description = 'Backfill title, clean path/filename for existing tracks';

    public function handle()
    {
        $count = 0;
        Track::chunkById(500, function($chunk) use (&$count){
            foreach ($chunk as $t) {
                $dirty = false;

                // title fallback
                if (empty($t->title) && !empty($t->filename)) {
                    $t->title = $t->filename;
                    $dirty = true;
                }

                // normalize slashes
                if ($t->relative_path) {
                    $norm = str_replace('\\','/',$t->relative_path);
                    if ($norm !== $t->relative_path) {
                        $t->relative_path = $norm;
                        $dirty = true;
                    }
                }

                if ($dirty) { $t->save(); $count++; }
            }
        });

        $this->info("Normalized {$count} tracks.");
        return 0;
    }
}