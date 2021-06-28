<?php

namespace OxygenModule\ImportExport\Strategy;

use RecursiveIteratorIterator;
use Symfony\Component\Console\Output\OutputInterface;

interface ImportStrategy {

    /**
     * Returns an iterator over the items in the backup.
     * This might require extracting an archive.
     * @return RecursiveIteratorIterator
     */
    public function getFiles(string $path, OutputInterface $output);

}
