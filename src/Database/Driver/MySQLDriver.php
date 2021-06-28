<?php

namespace OxygenModule\ImportExport\Database\Driver;

use OxygenModule\ImportExport\CommandRunner;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class MySQLDriver implements DriverInterface {
	protected $commandRunner;
	protected $database;
	protected $user;
	protected $password;
	protected $host;
	protected $port;
    protected $dumpCommandPath;
    protected $restoreCommandPath;
    private $defaultsFilePath;

	public function __construct(CommandRunner $console, $database, $user, $password, $host, $port, $dumpCommandPath, $restoreCommandPath) {
		$this->commandRunner = $console;
		$this->database = $database;
		$this->user = $user;
		$this->password = $password;
		$this->host = $host;
		$this->port = $port;
        $this->dumpCommandPath = $dumpCommandPath;
        $this->restoreCommandPath = $restoreCommandPath;
        $this->defaultsFilePath = storage_path('backups/.my.cnf');
	}

	private function getMysqlCmd(string $cmd = 'mysql') {
        file_put_contents($this->defaultsFilePath, sprintf("[client]\nuser=%s\npassword=%s\n", escapeshellarg($this->user), escapeshellarg($this->password)));

	    return [
            $this->dumpCommandPath . $cmd,
            '--defaults-extra-file=' . $this->defaultsFilePath,
            '--host=' . $this->host,
            '--port=' . $this->port,
            $this->database
        ];
	}

	public function dump($destinationFile, OutputInterface $output) {
        $output->writeln('Exporting contents using mysqldump');
		$process = new Process(array_merge($this->getMysqlCmd('mysqldump'), ['--no-tablespaces', '--no-create-info', '--complete-insert']));
        $process->run();
        if(!$process->isSuccessful()) {
            $output->writeln($process->getErrorOutput());
            throw new ProcessFailedException($process);
        }

		file_put_contents($destinationFile, $process->getOutput());

		unlink($this->defaultsFilePath);
	}

	public function truncateAllTables(OutputInterface $output) {
	    $process = new Process(array_merge($this->getMysqlCmd(), ['-Nse', 'show tables']));
	    $process->run();
	    if(!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $tables = explode("\n", $process->getOutput());
	    $output->writeln('Found ' . count($tables) . ' tables in database. Truncating them all.');
        foreach($tables as $table) {
            if($table == '') { continue; }
            $process = new Process(array_merge($this->getMysqlCmd(), ['-e', 'SET foreign_key_checks = 0; truncate table `' . $table . '`']));
            $output->writeln('Truncating table ' . $table);
            $this->commandRunner->run($process, $output);
        }
        $output->writeln('All tables truncated');
    }

	public function restore($sourceFile, OutputInterface $output) {
	    $this->truncateAllTables($output);

        $process = new Process($this->getMysqlCmd());
        $input = fopen($sourceFile, 'r');
        $process->setInput($input);

        $output->writeln('Loading data from sql dump file');
        $this->commandRunner->run($process, $output);

        fclose($input);
        unlink($this->defaultsFilePath);
	}

	public function getFileExtension(): string {
		return 'sql';
	}

}
