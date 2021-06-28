<?php

namespace OxygenModule\ImportExport\Strategy;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class PHPZipImportStrategy implements ImportStrategy {

    /**
     * @var ZipArchive
     */
    private $extractedDirectories = [];

    /**
     * Returns an iterator over files inside the extracted zip file pointed at by `$path`.
     *
     * @param string $path path to the zip file
     * @param OutputInterface $output write log output
     * @return RecursiveIteratorIterator
     * @throws Exception
     */
    public function getFiles(string $path, OutputInterface $output) {
        if(!isset($this->extractedDirectories[$path])) {
            $zip = new ZipArchive();
            if(!$zip->open($path, 0)) {
                throw new Exception("Failed to open zip file");
            }
            $tempDir = (new TemporaryDirectory())->create();
            $output->writeln('PHPZipImport: extracting `.zip` to ' . $tempDir->path());
            if(!$zip->extractTo($tempDir->path())) {
                throw new Exception("Failed to extract zip file");
            }
            $this->extractedDirectories[$path] = $tempDir;
        }

        return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->extractedDirectories[$path]->path()));
    }

}
