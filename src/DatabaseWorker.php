<?php

namespace OxygenModule\ImportExport;

use OxygenModule\ImportExport\Database\DatabaseManager;
use Exception;
use Illuminate\Config\Repository;

class DatabaseWorker implements WorkerInterface {

    /**
     * Constructs the DatabaseWorker.
     *
     * @param Repository  $config
     * @param DatabaseManager $manager
     */
    public function __construct(Repository $config, DatabaseManager $manager) {
        $this->config = $config;
        $this->database = $manager;
    }

    /**
     * Returns an array of files to add to the archive.
     *
     * @param string $backupKey
     * @return array
     * @throws Exception if the database failed to backup
     */
    public function getFiles($backupKey) {
        $filename = $this->config->get('oxygen.mod-import-export.path') . $backupKey . '.sql';

        $this->database->backup($filename);

        return [
            $filename => $backupKey . '.sql'
        ];
    }

    /**
     * Cleans up any temporary files that were created after they have been added to the ZIP archive.
     *
     * @param string $backupKey
     * @return void
     */
    public function cleanFiles($backupKey) {
        $filename = $this->config->get('oxygen.mod-import-export.path') . $backupKey . '.sql';

        if(file_exists($filename)) {
            unlink($filename);
        }
    }

}