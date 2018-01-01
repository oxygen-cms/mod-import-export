<?php

namespace OxygenModule\ImportExport\Database\Driver;

use OxygenModule\ImportExport\Console;

class MySQLDriver implements DriverInterface {
	protected $console;
	protected $database;
	protected $user;
	protected $password;
	protected $host;
	protected $port;
    protected $dumpCommandPath;
    protected $restoreCommandPath;

	public function __construct(Console $console, $database, $user, $password, $host, $port, $dumpCommandPath, $restoreCommandPath) {
		$this->console = $console;
		$this->database = $database;
		$this->user = $user;
		$this->password = $password;
		$this->host = $host;
		$this->port = $port;
        $this->dumpCommandPath = $dumpCommandPath;
        $this->restoreCommandPath = $restoreCommandPath;
	}

	public function dump($destinationFile) {
		$command = sprintf('%smysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
			$this->dumpCommandPath,
			escapeshellarg($this->user),
			escapeshellarg($this->password),
			escapeshellarg($this->host),
			escapeshellarg($this->port),
			escapeshellarg($this->database),
			escapeshellarg($destinationFile)
		);

		$this->console->run($command);
	}

	public function restore($sourceFile) {
		$command = sprintf('%smysql --user=%s --password=%s --host=%s --port=%s %s < %s',
			$this->restoreCommandPath,
			escapeshellarg($this->user),
			escapeshellarg($this->password),
			escapeshellarg($this->host),
			escapeshellarg($this->port),
			escapeshellarg($this->database),
			escapeshellarg($sourceFile)
		);

		$this->console->run($command);
	}

	public function getFileExtension() {
		return 'sql';
	}

}
