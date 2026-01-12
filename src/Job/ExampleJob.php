<?php

namespace ProKerja\Job;

use ProKerja\Contracts\JobInterface;

class ExampleJob implements JobInterface
{

    public function __construct(
        private int $id
    ) {}

    public function handle(): void
    {
        file_put_contents(
            __DIR__ . '/../../worker.log',
            "Realize job {$this->id}\n",
            FILE_APPEND
        );
    }

    public function payload(): array
    {
        return [
            'id' => $this->id
        ];
    }


}