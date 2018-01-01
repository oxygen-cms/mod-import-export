<?php

namespace OxygenModule\ImportExport\Strategy;

use ZipArchive;

class PHPZipExportStrategy implements ExportStrategy {

    /**
     * Constructs a new PHPZipStrategy
     */
    public function __construct($key, $path) {
        $this->key = $key;
        $this->path = $path . '.zip';
        $this->zip = new ZipArchive();
        if(!$this->zip->open($this->path, ZipArchive::CREATE)) {
            throw new Exception("Failed to create Zip file");
        }
    }

    public function commit() {
        if(!$this->zip->close()) {
            throw new Exception("Failed To close Zip file");
        }
    }

    /**
     * Returns an array of files to add to the backup.
     *
     * @param string $path the path to add
     * @param string $internalPath where it should be placed inside the `.zip`
     * @throws \Exception if the files could not be added
     */
    public function addFile($path, $relativeToDir) {
        if(!file_exists($path)) {
            throw new FileNotFoundException($path);
        }

        // turn this into a relative path
        if(!starts_with($path, $relativeToDir)) {
            throw new \Exception($path . ' is not inside ' . $relativeToDir);
        }
        $relativePath = substr($path, strlen($relativeToDir));

        if(!$this->zip->addFile($path, $relativePath)) {
            throw new Exception('Zip failed to add file: ' . $path);
        }
    }

    public function getKey() {
        return $this->key;
    }

}