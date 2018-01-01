<?php

namespace OxygenModule\ImportExport\Database;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use OxygenModule\ImportExport\Strategy\ExportStrategy;
use OxygenModule\ImportExport\Strategy\ImportStrategy;
use OxygenModule\ImportExport\WorkerInterface;
use OxygenModule\ImportExport\Console;

class DatabaseWorker implements WorkerInterface {

    public function __construct(Repository $config, Filesystem $filesystem, $database = null) {
        $this->config = $config;
        $this->files = $filesystem;
        $this->console = new Console();
        $this->filename =
        $this->database = $this->getDatabase($database);
    }

    /**
     * Returns an array of files to add to the archive.
     *
     * @param ExportStrategy $strategy
     * @throws Exception if the database failed to backup
     */
    public function export(ExportStrategy $strategy) {
        $filename = $this->getFilename($strategy);

        $this->createDumpFolderIfDoesNotExist($filename);

        try {
            $this->database->dump($filename);
        } catch(\Exception $e) {
            throw new DatabaseDumpException($e);
        }

        $strategy->addFile($filename, dirname($filename));
    }

    /**
     * Cleans up any temporary files that were created after they have been added to the ZIP archive.
     *
     * @return void
     */
    public function postExport(ExportStrategy $strategy) {
        $filename = $this->getFilename($strategy);

        if(file_exists($filename)) {
            unlink($filename);
        }
    }

    protected function getFilename(ExportStrategy $strategy) {
        return $this->config->get('oxygen.mod-import-export.path')
             . app()->environment()
             . '.'
             . $this->database->getFileExtension();
    }

    /**
     * Cleans up any temporary files that were created after they have been added to the ZIP archive.
     *
     * @param ImportStrategy $zip
     */
    public function import(ImportStrategy $strategy) {
        $files = $strategy->getFiles();
        foreach ($files as $file) {
            if (
                !$file->isDir() &&
                in_array($file->getExtension(), ['sql', 'sqlite', 'dump'])
            ) {
                $path = $file->getPathname();

                if(app()->runningInConsole()) {
                    echo 'Importing database file from ' . $path . "\n";
                }

                try {
                    $this->database->restore($path);
                } catch(\Exception $e) {
                    throw new DatabaseRestoreException($e);
                }
            }
        }
    }

    protected function getDatabase($database) {
        $database = $database ? : $this->config->get('database.default');
        $realConfig = $this->config->get('database.connections.' . $database);

        return $this->getDatabaseDriver($realConfig);
    }

    protected function getDatabaseDriver(array $config) {
        switch ($config['driver']) {
            case 'mysql':
                $port = isset($config['port']) ? $config['port'] : 3306;
                return new Driver\MySQLDriver(
                    $this->console,
                    $config['database'],
                    $config['username'],
                    $config['password'],
                    $config['host'],
                    $port,
                    $this->config->get('backup.mysql.dumpCommandPath'),
                    $this->config->get('backup.mysql.restoreCommandPath')
                );
                break;
            case 'sqlite':
                return new Driver\SqliteDriver(
                    $this->console,
                    $config['database']
                );
                break;
            case 'pgsql':
                return new Driver\PostgresDriver(
                    $this->console,
                    $config['database'],
                    $config['username'],
                    $config['password'],
                    $config['host']
                );
                break;
            default:
                throw new \Exception('Database driver not supported yet');
                break;
        }
    }

    protected function createDumpFolderIfDoesNotExist($filename) {
        $path = dirname($filename);

        if (!$this->files->exists($path)) {
            $this->files->makeDirectory($path);
        }
    }

}