<?php

namespace OxygenModule\ImportExport;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Console {

    public function run($command) {
        $process = new Process($command);
        $process->setTimeout(999999999);
        $process->run();

        if(app()->runningInConsole()) {
            echo $process->getOutput() . "\n";
            echo $process->getErrorOutput() . "\n";
        }

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

}