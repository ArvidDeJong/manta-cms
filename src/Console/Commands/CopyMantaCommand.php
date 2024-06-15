<?php

namespace Manta\MantaCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyMantaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manta:copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kopieer de map "manta" naar de root van het Laravel project';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sourcePath = __DIR__ . '/../../../content/manta';
        $destinationPath = base_path('manta');

        if (File::copyDirectory($sourcePath, $destinationPath)) {
            $this->info('De map "manta" is succesvol gekopieerd naar de root.');
        } else {
            $this->error('Er is iets misgegaan bij het kopiëren van de map.');
        }

        return 0;
    }
}
