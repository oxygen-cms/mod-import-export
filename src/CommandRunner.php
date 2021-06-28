<?php

namespace OxygenModule\ImportExport;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CommandRunner {

    public function run(Process $process, OutputInterface $output) {
        $process->run(function ($type, $buffer) use($output) {
            $output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

}
