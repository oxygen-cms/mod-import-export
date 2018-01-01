<?php

namespace OxygenModule\ImportExport;

use ZipArchive;
use OxygenModule\ImportExport\Strategy\ExportStrategy;
use OxygenModule\ImportExport\Strategy\ImportStrategy;

interface WorkerInterface {

    /**
     * Adds files to the backup.
     *
     * @param Strategy $strategy
     * @throws \Exception if the files could not be loaded or generated
     */
    public function export(ExportStrategy $strategy);

    /**
     * Cleans up any temporary files that were created after they have been added to the ZIP archive.
     *
     * @param Strategy $strategy
     * @return void
     */
    public function postExport(ExportStrategy $strategy);

    /**
     * Imports content from the backup.
     */
    public function import(ImportStrategy $strategy);

}