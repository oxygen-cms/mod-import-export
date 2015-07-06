<?php

namespace OxygenModule\ImportExport;

use Exception;
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
     * Constructs the BackupManager.
     *
     * @param string$environment
     */

    public function __construct($environment) {
        $this->environment = $environment;
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
        $folder = storage_path() . '/backups/';
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