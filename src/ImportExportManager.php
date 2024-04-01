<?php

namespace OxygenModule\ImportExport;

use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use OxygenModule\ImportExport\Strategy\ImportStrategy;
use OxygenModule\ImportExport\Strategy\ExportStrategy;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ImportExportManager {

    /**
     * An array of objects that will actually get the stuff to back up.
     *
     * @var array
     */
    protected $workers;

    /**
     * Current environment.
     *
     * @var string
     */

    protected $environment;

    /**
     * @var Repository
     */
    private $config;
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var ExportStrategy
     */
    private $exportStrategy;
    /**
     * @var ImportStrategy
     */
    private $importStrategy;

    /**
     * Constructs the BackupManager.
     *
     * @param Repository $config
     * @param Application $app
     * @param ImportStrategy $importStrategy
     * @param ExportStrategy $exportStrategy
     */
    public function __construct(Repository $config, Application $app, ImportStrategy $importStrategy, ExportStrategy $exportStrategy) {
        $this->environment = $app->environment();
        $this->config = $config;
        $this->exportStrategy = $exportStrategy;
        $this->importStrategy = $importStrategy;
        $this->output = new BufferedOutput();
    }

    /**
     * Overrides the log output used by import-export tasks
     *
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output) {
        if($this->output instanceof BufferedOutput)
        {
            $output->write($this->output->fetch());
        }
        $this->output = $output;
    }

    /**
     * Adds a worker.
     *
     * @param WorkerInterface $worker
     */
    public function addWorker(WorkerInterface $worker) {
        $this->workers[] = $worker;
    }

    /**
     * Makes a backup of the system.
     *
     * @throws Exception
     */
    public function export() {
        $folder = $this->config->get('oxygen.mod-import-export.path');
        if(!file_exists($folder)) {
            mkdir($folder);
        }
        $path = $folder . $this->environment;
        if(!file_exists($path)) {
            mkdir($path);
        }

        $this->exportStrategy->create($path);

        foreach($this->workers as $worker) {
            $files = $worker->export($this->output);
            foreach($files as $localPath => $path) {
                $this->output->writeln('Adding file: ' . $path);
                $this->exportStrategy->addFile($path, $localPath);
            }
        }

        $this->exportStrategy->commit();

        foreach($this->workers as $worker) {
            $worker->postExport($this->output);
        }
    }

    /**
     * Imports content from the ZIP file at the given path.
     *
     * @param string $path
     * @throws FileNotFoundException if the zip file couldn't be read
     * @throws Exception
     */
    public function import(string $path) {
        if(!file_exists($path)) {
            throw new FileNotFoundException($path . ' not found');
        }

        foreach($this->workers as $worker) {
            $files = $this->importStrategy->getFiles($path, $this->output);
            $worker->import($files, $this->output);
        }
    }

    public function getExportStrategy(): ExportStrategy {
        return $this->exportStrategy;
    }

}
