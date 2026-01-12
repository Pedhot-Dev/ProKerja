# ProKerja

**ProKerja** is a lightweight PHP job & queue framework focused on **declaration-first task orchestration**.

It provides a clean, minimal, and transparent job queue system built on top of **PHP + SQLite + Symfony Console**, without relying on heavy frameworks, magic containers, or hidden execution layers.

ProKerja is designed to be:
- technically correct
- predictable
- framework-agnostic
- usable in real projects

Any interpretation beyond what the system explicitly guarantees is **outside its responsibility**.

---

## Core Concept

ProKerja separates **declaration** from **realization**.

- Declaring a job means it is **registered into the system**
- Realizing a job depends entirely on **worker availability**
- The system **does not promise execution**, only orchestration

This is not a limitation.  
This is the contract.

---

## Features

- Job declaration & dispatch
- Persistent queue (SQLite)
- Long-running worker process
- Explicit job lifecycle:
    - `pending`
    - `processing`
    - `completed`
    - `failed`
- Symfony Console–based CLI
- Zero framework lock-in
- No background magic

---

## Requirements

- PHP 8.0+
- PDO with SQLite enabled
- Composer

---

## Installation

```bash
git clone https://github.com/yourname/prokerja.git
cd prokerja
composer install
chmod +x bin/prokerja or php bin/prokerja
```

## Basic Usage
### 1. Declare Jobs via CLI
```
php bin/prokerja declare:job
```
This command will:
- create job entries
- store them in the queue
- return immediately

A successful declaration means:
- the job exists
- the job is persisted
- the system acknowledges it

It **does not** mean the job has been executed.

### 2. Start Worker (Realization Process)
```
php bin/prokerja realization:start
```
This starts a blocking worker process that:
- continuously checks the queue
- processes pending jobs
- updates job status

If the queue is empty, the worker will remain idle and wait.

### 3. View Queue Report
```
php bin/prokerja report
```
Example output:
```
ProKerja Report
pending: 10
processing: 0
completed: 2
failed: 0
```
This report reflects **system state**, not execution guarantees.

## Creating Your Own Job
### 1. Create a Job Class
All jobs must implement `JobInterface`.
```php
<?php

namespace App\Jobs;

use ProKerja\Contracts\JobInterface;

final class SendEmailJob implements JobInterface
{
    public function __construct(
        private int $userId
    ) {}

    public function handle(): void
    {
        // Do the actual work here
        file_put_contents(
            'worker.log',
            "Email sent to user {$this->userId}\n",
            FILE_APPEND
        );
    }

    public function payload(): array
    {
        return [
            'userId' => $this->userId,
        ];
    }
}
```
**Rules for Jobs:**
- Must be instantiable from payload data
- Must not perform work inside the constructor
- All heavy work must be inside `handle()`

### Dispatching Jobs (Vanilla PHP)
You can dispatch jobs without using the CLI.
```php
<?php

require 'vendor/autoload.php';

use ProKerja\Queue\DeclarationQueue;
use ProKerja\Queue\Dispatcher;
use App\Jobs\SendEmailJob;

$queue = new DeclarationQueue(__DIR__ . '/prokerja.sqlite');
$dispatcher = new Dispatcher($queue);

$dispatcher->dispatch(
    new SendEmailJob(42)
);

echo "Job declared\n";
```
Dispatching:
- does not execute the job
- does not require a running worker
- always returns immediately

### Running Worker Without CLI (Vanilla)
```php
<?php

require 'vendor/autoload.php';

use ProKerja\Queue\DeclarationQueue;
use ProKerja\Worker\Worker;

$queue = new DeclarationQueue(__DIR__ . '/prokerja.sqlite');
$worker = new Worker($queue);

$worker->run(function () {
    echo "Waiting for ecosystem readiness...\n";
});
```
This behaves exactly like `realization:start`.

## Important Notes
### SQLite Path Consistency
All entry points **must use the same SQLite database path**.

Recommended:
```php
define('PROKERJA_DB', realpath(__DIR__ . '/prokerja.sqlite'));
```
Using different paths will result in:
- jobs not being picked up
- empty reports
- inconsistent behavior

## Design Principles
- Explicit over implicit
- No hidden execution
- No automatic scaling assumptions
- No promise of completion
- Transparent failure states

If a job does not run, the system is still functioning correctly.

## What ProKerja Is NOT
- Not a Laravel Queue replacement
- Not a distributed task system
- Not a scheduler
- Not a background daemon manager
- Not a guarantee engine

If you need those features, use a different tool.

## Philosophy
ProKerja guarantees **registration**, not **realization**.

It records intent, tracks state, and reports status.
Execution depends entirely on environment readiness.

The framework does exactly what it claims — nothing more.

## License
Copyright 2026 Pedhot-Dev

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
