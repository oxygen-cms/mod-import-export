<?php

namespace OxygenModule\ImportExport\Strategy;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class PHPZipImportStrategy implements ImportStrategy {

    public function __construct($path) {
        $this->extracted = false;
        $this->path = $path;
        $this->zip = new \ZipArchive();
        if(!$this->zip->open($path, 0)) {
            throw new Exception("Failed to open zip file");
        }
        $pathparts = pathinfo($path);
        $this->extractLocation = $pathparts['dirname'] . '/' . $pathparts['filename'];
        if(app()->runningInConsole()) {
            echo 'Extracting `.zip` to ' . $this->extractLocation . "\n";
        }
        if(!$this->zip->extractTo($this->extractLocation)) {
            throw new Exception("Failed to extract zip file");
        }
    }

    public function getFiles() {
        return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->extractLocation));
    }

}