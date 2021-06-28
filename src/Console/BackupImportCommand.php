<?php

namespace OxygenModule\ImportExport\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use OxygenModule\ImportExport\ImportExportManager;

class BackupImportCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:import {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports website content from a backup file.';

    /**
     * Execute the console command.
     *
     * @param ImportExportManager $manager
     * @return void
     * @throws FileNotFoundException
     */
    public function handle(ImportExportManager $manager) {
        $this->info('Importing backup...');

        $manager->setOutput($this->output);
        $manager->import($this->argument('path'));

        $this->info('Successfully imported');
    }

}
