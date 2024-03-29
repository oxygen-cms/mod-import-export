<?php

namespace OxygenModule\ImportExport\Strategy;

use Illuminate\Support\Facades\File;
use ZipArchive;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Exception;
use Illuminate\Support\Str;

class PHPZipExportStrategy implements ExportStrategy {
    /**
     * @var ZipArchive
     */
    private $zip;

    /**
     * @var string
     */
    private $path;

    /**
     * Constructs a new PHPZipExportStrategy
     */
    public function __construct() {
        $this->zip = new ZipArchive();
    }

    /**
     * @param string $folder
     * @throws Exception
     */
    public function create(string $folder) {
        $this->path = $folder . '/' . date('y-m-d-H-i-s') . '.zip';
        if(!$this->zip->open($this->path, ZipArchive::CREATE)) {
            throw new Exception("Failed to create Zip file");
        }
    }

    /**
     * @throws Exception
     */
    public function commit() {
        if(!$this->zip->close()) {
            throw new Exception("Failed To close Zip file");
        }
    }

    /**
     * Returns an array of files to add to the backup.
     *
     * @param string $path the path to add
     * @param string $localPath where it should be placed inside the `.zip`
     * @throws Exception if the files could not be added
     */
    public function addFile(string $path, string $localPath) {
        if(!file_exists($path)) {
            throw new FileNotFoundException($path);
        }

        if(!$this->zip->addFile($path, $localPath)) {
            throw new Exception('Zip failed to add file: ' . $path);
        }
    }

    /**
     * Returns the path to a backup file which can be downloaded.
     *
     * @return string
     */
    public function getDownloadableFile(): string {
        return $this->path;
    }

}
