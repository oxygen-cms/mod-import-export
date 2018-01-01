<?php

namespace OxygenModule\ImportExport\Strategy;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DuplicityStrategy implements ExportStrategy {

    /**
     * Constructs a new PHPZipStrategy
     */
    public function __construct($key, $path) {
        $this->key = $key;
        $this->path = $path;
        $this->files = [];
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
        if(!is_dir($relativeToDir)) {
            throw new FileNotFoundException($relativeToDir);
        }
        $relativeToDir = rtrim($relativeToDir, '/') . '/';

        if(!isset($this->files[$relativeToDir])) {
            $this->files[$relativeToDir] = [];
        }
        $this->files[$relativeToDir][] = $path;
    }

    public function commit() {
        $list = '';
        $base = rtrim(base_path(), '/') . '/';
        foreach($this->files as $relativeToDir => $files) {
            foreach($files as $file) {
                // turn this into a relative path
                if(!starts_with($file, $base)) {
                    throw new \Exception($file . ' is not inside ' . $base);
                }
                //$file = substr($file, strlen($base));
                $list .= '--include ' . escapeshellarg($file) . ' ';
            }
        }

        $cmd = 'duplicity --no-encryption ' . $list . ' --exclude \'**\' ' . escapeshellarg($base) . ' ' . escapeshellarg('file://' . $this->path);

        $process = new Process($cmd, $base);
        $process->run();

        if(app()->runningInConsole()) {
            echo $process->getOutput() . "\n";
            echo $process->getErrorOutput() . "\n";
        }
        if(!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }


    public function getKey() {
        return $this->key;
    }

}