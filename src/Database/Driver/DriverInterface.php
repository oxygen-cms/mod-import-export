<?php

namespace OxygenModule\ImportExport\Database\Driver;

use Symfony\Component\Process\Exception\ProcessFailedException;

interface DriverInterface
{
	/**
	 * Create a database dump
	 *
	 * @throws ProcessFailedException if the dump failed
	 */
	public function dump($destinationFile);

	/**
	 * Restore a database dump
	 *
	 * @throws ProcessFailedException if the restore failed
	 */
	public function restore($sourceFile);

	/**
	 * Return the file extension of a dump file (sql, ...)
	 *
	 * @return string
	 */
	public function getFileExtension();
}