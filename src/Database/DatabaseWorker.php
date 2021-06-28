<?php

namespace OxygenModule\ImportExport\Database;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use OxygenModule\ImportExport\WorkerInterface;
use OxygenModule\ImportExport\CommandRunner;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseWorker implements WorkerInterface {

    /**
     * @var Repository
     */
    private $config;

    /**
     * @var Filesystem
     */
    private $files;

    /**
     * @var CommandRunner
     */
    private $commandRunner;

    /**
     * @var Driver\MySQLDriver|Driver\PostgresDriver
     */
    private $database;

    /**
     * @param Repository $config
     * @param Filesystem $filesystem
     * @param null $database
     * @throws Exception
     */
    public function __construct(Repository $config, Filesystem $filesystem, $database = null) {
        $this->config = $config;
        $this->files = $filesystem;
        $this->commandRunner = new CommandRunner();
        $this->database = $this->getDatabase($database);
    }

    /**
     * Returns an array of files to add to the archive.
     *
     * @param OutputInterface $output
     * @return array
     * @throws Exception
     */
    public function export(OutputInterface $output): array {
        $filename = $this->getFilename();

        $this->createDumpFolderIfDoesNotExist($filename);

        $this->database->dump($filename, $output);

        return [
            basename($filename) => $filename
        ];
    }

    /**
     * Cleans up any temporary files that were created after they have been added to the ZIP archive.
     *
     * @return void
     */
    public function postExport(OutputInterface $output) {
        $filename = $this->getFilename();

        if(file_exists($filename)) {
            unlink($filename);
        }
    }

    protected function getFilename(): string {
        return $this->config->get('oxygen.mod-import-export.path')
             . app()->environment()
             . '.'
             . $this->database->getFileExtension();
    }

    /**
     * Imports content to the database from a .zip archive.
     *
     * @param RecursiveIteratorIterator $files
     * @param OutputInterface $output
     * @throws DatabaseRestoreException
     */
    public function import(RecursiveIteratorIterator $files, OutputInterface $output) {
        foreach ($files as $file) {
            if (
                !$file->isDir() &&
                in_array($file->getExtension(), ['sql', 'sqlite', 'dump'])
            ) {
                $path = $file->getPathname();

                $output->writeln('DatabaseWorker: loading from ' . $path);

                try {
                    $this->database->restore($path, $output);
                } catch(Exception $e) {
                    throw new DatabaseRestoreException($e);
                }
            }
        }
    }

    /**
     * @param mixed $database
     * @return Driver\MySQLDriver|Driver\PostgresDriver
     * @throws Exception if the driver is not supported yet
     */
    protected function getDatabase($database) {
        $database = $database ? $database : $this->config->get('database.default');
        $realConfig = $this->config->get('database.connections.' . $database);

        return $this->getDatabaseDriver($realConfig);
    }

    /**
     * @param array $config
     * @return Driver\MySQLDriver|Driver\PostgresDriver
     * @throws Exception if the driver is not supported yet
     */
    protected function getDatabaseDriver(array $config) {
        switch ($config['driver']) {
            case 'mysql':
                $port = $config['port'] ?? 3306;
                return new Driver\MySQLDriver(
                    $this->commandRunner,
                    $config['database'],
                    $config['username'],
                    $config['password'],
                    $config['host'],
                    $port,
                    $this->config->get('backup.mysql.dumpCommandPath'),
                    $this->config->get('backup.mysql.restoreCommandPath')
                );
                break;
            case 'pgsql':
                return new Driver\PostgresDriver(
                    $this->commandRunner,
                    $config['database'],
                    $config['username'],
                    $config['password'],
                    $config['host']
                );
            default:
                throw new Exception('Database driver not supported yet');
        }
    }

    protected function createDumpFolderIfDoesNotExist($filename) {
        $path = dirname($filename);

        if (!$this->files->exists($path)) {
            $this->files->makeDirectory($path);
        }
    }

}
