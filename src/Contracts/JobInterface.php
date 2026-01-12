<?php

namespace ProKerja\Contracts;

interface JobInterface
{

    public function handle(): void;
    public function payload(): array;

}