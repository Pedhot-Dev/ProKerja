<?php

namespace ProKerja\Command;

use ProKerja\Queue\DeclarationQueue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ReportCommand extends Command
{

    protected function configure(): void
    {
        $this->setName('report')
            ->setDescription('Send report');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queue = new DeclarationQueue(__DIR__ . '/../../prokerja.sqlite');
        $summary = $queue->summary();

        $output->writeln('<info>ProKerja Report</info>');

        foreach ($summary as $status => $count) {
            $output->writeln(sprintf('%s: %d', $status, $count));
        }

        return Command::SUCCESS;
    }

}