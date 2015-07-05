<?php

namespace OxygenModule\ImportExport;

interface WorkerInterface {

    /**
     * Returns an array of files to add to the archive.
     *
     * @param string $backupKey
     * @return mixed
     * @throws \Exception if the files could not be loaded or generated
     */
    public function getFiles($backupKey);

} 