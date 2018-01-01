<?php

namespace OxygenModule\ImportExport\Strategy;

interface ExportStrategy {

    /**
     * Returns an array of files to add to the backup.
     *
     * @param string $path the path to add
     * @throws \Exception if the files could not be added
     */
    public function addFile($path, $relativeToDir);

    /**
     * Returns the key identifying this particular backup.
     * @return string
     */
    public function getKey();

    /**
     * Commits the changes, actually performing the operations.
     */
    public function commit();

}