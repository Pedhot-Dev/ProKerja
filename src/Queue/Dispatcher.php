<?php

namespace ProKerja\Queue;

use ProKerja\Contracts\JobInterface;

final class Dispatcher
{

    public function __construct(
        private DeclarationQueue $queue
    ) {}

    public function dispatch(JobInterface $job): void
    {
        $this->queue->push($job);
    }

}