<?php

namespace OxygenModule\ImportExport\Strategy;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Process\Process;

class SystemZipStrategy implements ExportStrategy {

    /**
     * Constructs a new PHPZipStrategy
     */
    public function __construct($key, $path) {
        $this->key = $key;
        $this->path = $path . '.zip';
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
        foreach($this->files as $relativeToDir => $files) {
            $list = '';

            foreach($files as $file) {
                // turn this into a relative path
                if(!starts_with($file, $relativeToDir)) {
                    throw new \Exception($file . ' is not inside ' . $relativeToDir);
                }
                $file = substr($file, strlen($relativeToDir));
                $list .= escapeshellarg($file) . ' ';
            }

            $process = new Process(
                'zip ' . escapeshellarg($this->path) . ' ' . $list,
                $relativeToDir
            );
            $process->run();
            echo $process->getOutput() . "\n";
            echo $process->getErrorOutput() . "\n";
            if(!$process->isSuccessful()) {
                echo 'error' . "\n";
            }
        }
    }

    public function getKey() {
        return $this->key;
    }

}