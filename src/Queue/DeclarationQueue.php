<?php

namespace ProKerja\Queue;

use PDO;
use ProKerja\Contracts\JobInterface;
use ProKerja\Job\StatusEnum;

final class DeclarationQueue
{

    private PDO $db;

    public function __construct(string $path)
    {
        $this->db = new PDO('sqlite:' . $path);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->init();
    }

    private function init(): void
    {
        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS jobs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                job TEXT NOT NULL,
                payload TEXT NOT NULL,
                status TEXT NOT NULL
            )'
        );
    }

    public function push(JobInterface $job): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO jobs (job, payload, status)
             VALUES (:job, :payload, :status)'
        );

        $stmt->execute([
            'job' => get_class($job),
            'payload' => json_encode($job->payload()),
            'status' => StatusEnum::PENDING->value,
        ]);
    }

    public function pop(): ?array
    {
        $stmt = $this->db->query(
            'SELECT * FROM jobs WHERE status = "pending" ORDER BY id ASC LIMIT 1'
        );

        $job = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$job) {
            return null;
        }

        $update = $this->db->prepare(
            'UPDATE jobs SET status = :status WHERE id = :id'
        );

        $update->execute([
            'status' => StatusEnum::PROCESSING->value,
            'id' => $job['id'],
        ]);

        return $job;
    }

    public function complete(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE jobs SET status = :status WHERE id = :id'
        );

        $stmt->execute([
            'status' => StatusEnum::COMPLETED->value,
            'id' => $id,
        ]);
    }

    public function fail(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE jobs SET status = :status WHERE id = :id'
        );

        $stmt->execute([
            'status' => StatusEnum::FAILED->value,
            'id' => $id,
        ]);
    }

    public function summary(): array
    {
        $stmt = $this->db->query(
            'SELECT status, COUNT(*) as total FROM jobs GROUP BY status'
        );

        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

}