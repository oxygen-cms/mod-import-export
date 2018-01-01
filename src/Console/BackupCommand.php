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
    protected $signature = 'backup:make {--strategy=php-zip : One of `php-zip`, `system-zip`, or `duplicity` }';

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

        switch($this->option('strategy')) {
            case 'php-zip':
                $strategy = new PHPZipExportStrategy();
                break;
            case 'system-zip':
                $strategy = new SystemZipStrategy();
                break;
            case 'duplicity':
                $strategy = new DuplicityStrategy();
                break;
            default:
                $this->error('unknown value for option --strategy');
                return;
        }

        $manager->export($strategy);

        $at = $strategy->getDownloadableFile();
        if($at == null) {
            $this->info('Backup did not create a single, downloadable file');
        } else {
            $this->info('Backup created at ' . $at);
        }

    }

}