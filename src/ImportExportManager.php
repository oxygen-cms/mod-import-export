<?php

namespace OxygenModule\ImportExport;

use Exception;
use Illuminate\Contracts\Config\Repository;
use ZipArchive;

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

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    private $config;

    /**
     * Constructs the BackupManager.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param string                                  $environment
     */

    public function __construct(Repository $config, $environment) {
        $this->environment = $environment;
        $this->config = $config;
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
        $filename = $folder . $key . '.zip';

        $zip = new ZipArchive();
        if($zip->open($filename, ZipArchive::CREATE)) {
            foreach($this->workers as $worker) {
                $files = $worker->getFiles($key);
                foreach($files as $realpath => $newpath) {
                    if(!$zip->addFile($realpath, basename($filename) . '/' . $newpath)) {
                        throw new Exception("Zip Failed to Add File");
                    }
                }
                $worker->cleanFiles($key);
            }

            if($zip->close()) {
                return $filename;
            } else {
                throw new Exception("Zip File Failed To Close");
            }
        } else {
            throw new Exception("Zip File Failed To Open");
        }
    }

}