<?php

namespace OxygenModule\ImportExport\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use OxygenModule\ImportExport\ImportExportManager;
use OxygenModule\ImportExport\Strategy\DuplicityStrategy;
use OxygenModule\ImportExport\Strategy\PHPZipExportStrategy;
use OxygenModule\ImportExport\Strategy\SystemZipStrategy;

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
    protected $description = 'Creates a backup of the website.';

    /**
     * Execute the console command.
     *
     * @param ImportExportManager $manager
     * @return void
     * @throws \Exception
     */
    public function handle(ImportExportManager $manager) {
        $manager->setOutput($this->output);

        $this->info('Creating backup...');
        $manager->export();

        $at = $manager->getExportStrategy()->getDownloadableFile();
        if($at == null) {
            $this->warn('Backup did not create a single downloadable file');
        } else {
            $this->info('Backup created at ' . $at);
        }

    }

}
