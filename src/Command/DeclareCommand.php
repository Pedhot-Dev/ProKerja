<?php

namespace ProKerja\Command;

use ProKerja\Job\ExampleJob;
use ProKerja\Queue\DeclarationQueue;
use ProKerja\Queue\Dispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DeclareCommand extends Command
{

    protected function configure(): void
    {
        $this->setName('declare:job')
            ->setDescription('Declare a job');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queue = new DeclarationQueue(__DIR__ . '/../../prokerja.sqlite');
        $dispatcher = new Dispatcher($queue);

        $dispatcher->dispatch(new ExampleJob(1));
        $dispatcher->dispatch(new ExampleJob(2));

        $output->writeln('<info>Job successfully declared</info>');
        $output->writeln('<comment>Waiting for ecosystem readiness for realization</comment>');

        return Command::SUCCESS;
    }


}