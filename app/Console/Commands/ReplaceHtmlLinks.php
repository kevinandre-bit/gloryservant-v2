<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ReplaceHtmlLinks extends Command
{
    protected $signature = 'replace:html-links';
    protected $description = 'Convert .html links to Laravel route() calls';

    public function handle()
    {
        $path = resource_path('views/admin_v2');
        $files = File::allFiles($path);

        foreach ($files as $file) {
            $content = File::get($file->getRealPath());

            $updated = preg_replace(
                '/href="([a-zA-Z0-9\-_]+)\.html"/',
                'href="{{ route(\'admin_v2.$1\') }}"',
                $content
            );

            if ($updated !== $content) {
                File::put($file->getRealPath(), $updated);
                $this->info("Updated: {$file->getFilename()}");
            }
        }

        $this->info('All .html links converted successfully.');
    }
}