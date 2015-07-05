<?php

namespace OxygenModule\ImportExport\Database;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;

class DatabaseManager {

    public function __construct(Repository $config, Filesystem $filesystem, $database = null) {
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->console = new Console();
        $this->database = $this->getDatabase($database);
    }

    public function backup($filename) {
        $this->createDumpFolderIfDoesNotExist($filename);

        if(!$this->database->dump($filename)) {
            throw new DatabaseDumpException("Failed to Backup Database");
        }
    }

    public function restore($filename) {
        if(!$this->database->restore($filename)) {
            throw new DatabaseDumpException("Failed to Restore Database");
        }
    }

    public function getDatabase($database) {
        $database = $database ? : $this->config->get('database.default');
        $realConfig = $this->config->get('database.connections.' . $database);

        return $this->getDatabaseDriver($realConfig);
    }

    public function getDatabaseDriver(array $config) {
        switch ($config['driver'])
        {
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

        if (!$this->filesystem->exists($path)) {
            $this->filesystem->makeDirectory($path);
        }
    }

}