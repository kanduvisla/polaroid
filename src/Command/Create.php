<?php

declare(strict_types=1);

namespace Kanduvisla\Polaroid\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Kanduvisla\Polaroid\Processor\Basic as BasicProcessor;

class Create extends Command {
    protected static $defaultName = 'create';

    protected function configure() {
        $this->setDescription('Create a Polaroid-like picture from a single JPG file');
        $this->addOption('input', 'i', InputOption::VALUE_REQUIRED, 'input file');
        $this->addOption('message', 'm', InputOption::VALUE_OPTIONAL, 'message');
        $this->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'output file');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $inputFile = $input->getOption('input');
        $outputFile = $input->getOption('output');
        if (!$outputFile) {
            $outputFile = 'new-' . basename($inputFile);
        }

        $message = (string)$input->getOption('message');

        $output->writeln(sprintf('%1$s => %2$s', $inputFile, $outputFile));

        $basicProcessor = new BasicProcessor();
        $basicProcessor->create($inputFile, $message, $outputFile);

        return 0;
    }
}
