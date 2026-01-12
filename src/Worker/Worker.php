<?php

namespace ProKerja\Worker;

use ProKerja\Queue\DeclarationQueue;

final class Worker
{

    public function __construct(
        private DeclarationQueue $queue
    ) {}

    public function run(callable $onIdle = null): void
    {
        while (true) {
            $jobData = $this->queue->pop();

            if (!$jobData) {
                if ($onIdle) {
                    $onIdle();
                }
                sleep(1);
                continue;
            }

            $jobClass = $jobData['job'];
            $payload = json_decode($jobData['payload'], true);

            try {
                $job = new $jobClass(...array_values($payload));
                $job->handle();
                $this->queue->complete($jobData['id']);
            } catch (\Throwable) {
                $this->queue->fail($jobData['id']);
            }
        }
    }

}