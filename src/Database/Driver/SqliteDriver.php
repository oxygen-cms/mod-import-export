<?php

namespace OxygenModule\ImportExport\Database\Driver;

use OxygenModule\ImportExport\Database\Console;

class SqliteDriver implements DriverInterface {
	protected $console;
	protected $databaseFile;

	public function __construct(Console $console, $databaseFile) {
		$this->console = $console;
		$this->databaseFile = $databaseFile;
	}

	public function dump($destinationFile) {
		$command = sprintf('cp %s %s',
			escapeshellarg($this->databaseFile),
			escapeshellarg($destinationFile)
		);

		return $this->console->run($command);
	}

	public function restore($sourceFile) {
		$command = sprintf('cp -f %s %s',
			escapeshellarg($sourceFile),
			escapeshellarg($this->databaseFile)
		);

		return $this->console->run($command);
	}

	public function getFileExtension() {
		return 'sqlite';
	}
}