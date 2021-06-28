<?php

namespace OxygenModule\ImportExport;

use Exception;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Output\OutputInterface;

interface WorkerInterface {

    /**
     * Adds files to the backup.
     *
     * @param OutputInterface $output
     * @return array of file paths to export (format: "local/path" => "global/path" )
     * @throws Exception if the files could not be loaded or generated
     */
    public function export(OutputInterface $output): array;

    /**
     * Cleans up any temporary files that were created after they have been added to the ZIP archive.
     *
     * @param OutputInterface $output
     * @return void
     */
    public function postExport(OutputInterface $output);

    /**
     * Imports content from the backup.
     * @param RecursiveIteratorIterator $files
     * @param OutputInterface $output
     * @return void
     */
    public function import(\RecursiveIteratorIterator $files, OutputInterface $output);

}
