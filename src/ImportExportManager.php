<?php

namespace OxygenModule\ImportExport;

use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
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
        $filename = $folder . $key . '.zip';

        $this->workWithZipFile($filename, ZipArchive::CREATE, function(ZipArchive $zip) use($key, $filename) {
            foreach($this->workers as $worker) {
                $files = $worker->export($key);
                foreach($files as $realpath => $newpath) {
                    if(!file_exists($realpath)) {
                        throw new FileNotFoundException($realpath);
                    }
                    if(!$zip->addFile($realpath, $key . '/' . $newpath)) {
                        throw new Exception('Zip Failed to Add File: ' . $realpath . ' => ' . $key . '/' . $newpath);
                    }
                }
            }
        });

        foreach($this->workers as $worker) {
            $worker->postExport($key);
        }
        $this->temporaryFilesToDelete[] = $filename;
        return $filename;
    }

    /**
     * Imports content from the ZIP file at the given path.
     *
     * @param $path
     * @throws \Exception if the zip file couldn't be read
     */
    public function import($path) {
        $zip = new ZipArchive();

        $this->workWithZipFile($path, 0, function(ZipArchive $zip) {
            foreach($this->workers as $worker) {
                $worker->import($zip);
            }
        });
    }

    /**
     * Works with the contents of a zip file.
     *
     * @param          $path
     * @param          $flags
     * @param callable $callback
     * @throws \Exception if the zip file failed to open or close
     */
    protected function workWithZipFile($path, $flags, callable $callback) {
        $zip = new ZipArchive();
        if($zip->open($path, $flags)) {
            $callback($zip);
            if(!$zip->close()) {
                throw new Exception("Failed To Close Zip File");
            }
        } else {
            throw new Exception("Failed to Open Zip File");
        }
    }

}