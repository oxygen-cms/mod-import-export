<?php

namespace OxygenModule\ImportExport\Strategy;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use ZipArchive;

class PHPZipImportStrategy implements ImportStrategy {

    /**
     * @var bool
     */
    private $extracted;

    /**
     * @var string
     */
    private $path;
    /**
     * @var TemporaryDirectory
     */
    private $tempDir;
    /**
     * @var ZipArchive
     */
    private $zip;

    /**
     * PHPZipImportStrategy constructor.
     * @param string $path
     * @throws Exception
     */
    public function __construct(string $path) {
        $this->extracted = false;
        $this->path = $path;
        $this->zip = new ZipArchive();
        if(!$this->zip->open($path, 0)) {
            throw new Exception("Failed to open zip file");
        }
        $this->tempDir = (new TemporaryDirectory())->create();
        if(app()->runningInConsole()) {
            echo 'Extracting `.zip` to ' . $this->tempDir->path() . "\n";
        }
        if(!$this->zip->extractTo($this->tempDir->path())) {
            throw new Exception("Failed to extract zip file");
        }
    }

    public function getFiles() {
        return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->tempDir->path()));
    }

}
