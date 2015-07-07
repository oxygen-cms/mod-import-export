<?php

namespace OxygenModule\ImportExport;

use ZipArchive;

interface WorkerInterface {

    /**
     * Returns an array of files to add to the archive.
     *
     * @param string $backupKey
     * @return mixed
     * @throws \Exception if the files could not be loaded or generated
     */
    public function export($backupKey);

    /**
     * Cleans up any temporary files that were created after they have been added to the ZIP archive.
     *
     * @param string $backupKey
     * @return void
     */
    public function postExport($backupKey);

    /**
     * Imports content from a zip file
     *
     * @param \ZipArchive $zip
     */
    public function import(ZipArchive $zip);

} 