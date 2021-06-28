<?php

namespace OxygenModule\ImportExport\Database\Driver;

use OxygenModule\ImportExport\CommandRunner;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PostgresDriver implements DriverInterface {
	protected $console;
	protected $database;
	protected $user;
	protected $password;
	protected $host;

	public function __construct(CommandRunner $console, $database, $user, $password, $host) {
		$this->console = $console;
		$this->database = $database;
		$this->user = $user;
		$this->password = $password;
		$this->host = $host;
	}

	public function dump($destinationFile, OutputInterface $output) {
		$process = Process::fromShellCommandLine(sprintf('PGPASSWORD=%s pg_dump -Fc --no-acl --no-owner -h %s -U %s %s > %s',
			escapeshellarg($this->password),
			escapeshellarg($this->host),
			escapeshellarg($this->user),
			escapeshellarg($this->database),
			escapeshellarg($destinationFile)
		));

		$this->console->run($process, $output);
	}

	public function restore($sourceFile, OutputInterface $output) {
		$process = Process::fromShellCommandLine(sprintf('PGPASSWORD=%s pg_restore --verbose --clean --no-acl --no-owner -h %s -U %s -d %s %s',
			escapeshellarg($this->password),
			escapeshellarg($this->host),
			escapeshellarg($this->user),
			escapeshellarg($this->database),
			escapeshellarg($sourceFile)
		));

		$this->console->run($process, $output);
	}

	public function getFileExtension(): string {
		return 'dump';
	}
}
