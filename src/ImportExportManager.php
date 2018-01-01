<?php

namespace OxygenModule\ImportExport;

use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use OxygenModule\ImportExport\Strategy\SystemZipStrategy;
use OxygenModule\ImportExport\Strategy\PHPZipImportStrategy;
use OxygenModule\ImportExport\Strategy\DuplicityStrategy;
use OxygenModule\ImportExport\Strategy\PHPZipExportStrategy;

class ImportExportManager {

    /**
     * An array of objects that will actually get the stuff to back up.
     *
     * @var array
     */
    protected $workers;

    /**
     * Current environment.
     *
     * @var string
     */

    protected $environment;

    protected $app;

    public $temporaryFilesToDelete;

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    private $config;

    /**
     * Constructs the BackupManager.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \Illuminate\Foundation\Application      $app
     * @param \OxygenModule\ImportExport\Strategy     $strategy
     * @param string                                  $environment
     */

    public function __construct(Repository $config, Application $app, $environment) {
        $this->environment = $environment;
        $this->config = $config;
        $this->app = $app;
        $this->temporaryFilesToDelete = [];
    }

    /**
     * Adds a worker.
     *
     * @param WorkerInterface $worker
     */

    public function addWorker(WorkerInterface $worker) {
        $this->workers[] = $worker;
    }

    /**
     * Returns a key for the backup.
     *
     * @return string
     */

    public function getBackupKey() {
        return $this->environment . '-' . date('y-m-d');
    }

    /**
     * Makes a backup of the system.
     *
     * @return string
     * @throws Exception
     */
    public function export() {
        $key = $this->getBackupKey();
        $folder = $this->config->get('oxygen.mod-import-export.path');
        if(!file_exists($folder)) {
            mkdir($folder);
        }
        $name = $folder . $key;

        $strategy = new PHPZipExportStrategy($key, $name);

        foreach($this->workers as $worker) {
            $worker->export($strategy);
        }

        if(app()->runningInConsole()) {
            echo "Comitting...\n";
        }
        $strategy->commit();
        if(app()->runningInConsole()) {
            echo "Comitted\n";
        }

        foreach($this->workers as $worker) {
            $worker->postExport($strategy);
        }

        return $name . '.zip';
    }

    /**
     * Imports content from the ZIP file at the given path.
     *
     * @param $path
     * @throws \Exception if the zip file couldn't be read
     */
    public function import($path) {
        if(!file_exists($path)) {
            throw new FileNotFoundException($path . ' not found');
        }
        $strategy = new PHPZipImportStrategy($path);

        foreach($this->workers as $worker) {
            $worker->import($strategy);
        }
    }

}