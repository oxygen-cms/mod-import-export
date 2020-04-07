<?php

namespace OxygenModule\ImportExport;

use Exception;
use OxygenModule\ImportExport\Strategy\ExportStrategy;
use OxygenModule\ImportExport\Strategy\ImportStrategy;

interface WorkerInterface {

    /**
     * Adds files to the backup.
     *
     * @param ExportStrategy $strategy
     * @throws Exception if the files could not be loaded or generated
     * @return void
     */
    public function export(ExportStrategy $strategy);

    /**
     * Cleans up any temporary files that were created after they have been added to the ZIP archive.
     *
     * @param ExportStrategy $strategy
     * @return void
     */
    public function postExport(ExportStrategy $strategy);

    /**
     * Imports content from the backup.
     * @param ImportStrategy $strategy
     * @return void
     */
    public function import(ImportStrategy $strategy);

}