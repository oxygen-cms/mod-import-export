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
     * Starts creating a backup under the specified path.
     */
    public function create($path);

    /**
     * Commits the changes, actually performing the operations.
     */
    public function commit();

    /**
     * Returns the path to a backup file which can be downloaded.
     *
     * @return string
     */
    public function getDownloadableFile();

}