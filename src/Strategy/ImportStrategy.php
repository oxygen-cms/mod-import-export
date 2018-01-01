<?php

namespace OxygenModule\ImportExport\Strategy;

interface ImportStrategy {

    /**
     * Returns an iterator over the items in the backup.
     * This might require extracting an archive.
     * @return \RecursiveIteratorIterator
     */
    public function getFiles();

}