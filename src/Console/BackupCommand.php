<?php

namespace OxygenModule\ImportExport\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use OxygenModule\ImportExport\ImportExportManager;

class BackupCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a backup of the website with Duplicity.';

    /**
     * Execute the console command.
     *
     * @param ImportExportManager $manager
     * @return mixed
     */
    public function handle(ImportExportManager $manager) {
        $this->info('Creating backup...');

        $path = $manager->export();

        $this->info('Backup created at ' . $path);
    }

}