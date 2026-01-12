<?php

namespace ProKerja\Command;

use ProKerja\Queue\DeclarationQueue;
use ProKerja\Worker\Worker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RealizationStartCommand extends Command
{

    protected function configure(): void
    {
        $this->setName('realization:start')
            ->setDescription('Start realization');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queue = new DeclarationQueue(__DIR__ . '/../../prokerja.sqlite');
        $worker = new Worker($queue);

        $worker->run(function () use ($output) {
            $output->writeln('<comment>Waiting for ecosystem readiness...</comment>');
        });

        return Command::SUCCESS;
    }

}