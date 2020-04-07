<?php

namespace OxygenModule\ImportExport\Strategy;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Support\Str;

class SystemZipStrategy implements ExportStrategy {
    /**
     * @var array
     */
    private $files;
    /**
     * @var string
     */
    private $path;

    /**
     * Constructs a new SystemZipStrategy
     */
    public function __construct() {
        $this->files = [];
    }

    public function create($folder) {
        $this->path = $folder . '/' . date('y-m-d-H-i-s') . '.zip';
        if(!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }
    }

    /**
     * Returns an array of files to add to the backup.
     *
     * @param string $path the path to add
     * @param string $relativeToDir where it should be placed inside the `.zip`
     * @throws Exception if the files could not be added
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

    /**
     * @throws Exception
     */
    public function commit() {
        foreach($this->files as $relativeToDir => $files) {
            $list = '';

            foreach($files as $file) {
                // turn this into a relative path
                if(!Str::startsWith($file, $relativeToDir)) {
                    throw new Exception($file . ' is not inside ' . $relativeToDir);
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

    /**
     * Returns the path to a backup file which can be downloaded.
     *
     * @return string
     */
    public function getDownloadableFile() {
        return $this->path;
    }

}