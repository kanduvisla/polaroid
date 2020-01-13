<?php

declare(strict_types=1);

namespace Kanduvisla\Polaroid\Command;

use SplFileObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Kanduvisla\Polaroid\Processor\Basic as BasicProcessor;

class Batch extends Command {
    protected static $defaultName = 'batch';

    protected function configure() {
        $this->setDescription('Create Polaroid-like pictures from a CSV file containing data');
        $this->addOption('input', 'i', InputOption::VALUE_REQUIRED, 'input CSV file');
        $this->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'output directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $inputFile = $input->getOption('input');
        $outputDir = $input->getOption('output');

        $basicProcessor = new BasicProcessor();
        $csv = new SplFileObject($inputFile);
        $csv->setFlags(SplFileObject::READ_CSV);
        $headers = $csv->fgetcsv();
        while($row = $csv->fgetcsv()) {
            if ($row === [null]) {
                continue;
            }
            $data = array_combine($headers, $row);
            $data['message'] = $data['message'] !== '' ?
                $data['message'] :
                str_replace(['.jpg', 'jpeg', '.JPG'], '', $data['filename']);
            $outputFile = $outputDir . '/' . $data['filename'];
            $output->writeln(sprintf('%1$s => %2$s', $data['filename'], $outputFile));
            $dir = dirname($inputFile);
            $basicProcessor->create(
                $dir . '/' . $data['filename'],
                $data['message'],
                $outputFile,
                $this->getOptions($data['options'])
            );
        }

        return 0;
    }

    private function getOptions(string $options): array
    {
        $options = explode(',', $options);
        $returnValue = [];
        foreach($options as $option) {
            $a = explode(':', $option);
            $returnValue[$a[0]] = $a[1];
        }
        return $returnValue;
    }
}
