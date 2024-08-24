<?php

namespace Qunique;

use RuntimeException;

abstract class BaseJob
{
    protected int $timeout;
    protected int $startTime;

    public function __construct()
    {
        $this->startTime = time();
    }

    public function __destruct()
    {
        $this->cleanupResources();
    }

    public function execute(int $timeout = 0): void
    {
        $this->timeout = $timeout;

        $pid = pcntl_fork();

        if ($pid === -1) {
            throw new RuntimeException('Could not fork process.');
        }

        if ($pid === 0) {
            // Дочерний процесс
            $this->handle();
            exit(0);
        } else {
            // Родительский процесс
            $start_time = time();
            while (true) {
                if ((time() - $start_time) > $timeout && $timeout > 0) {
                    posix_kill($pid, SIGTERM);
                    throw new RuntimeException('Task execution exceeded the timeout limit.');
                }

                // Проверяем статус дочернего процесса
                $status = null;
                $res = pcntl_waitpid($pid, $status, WNOHANG);
                if ($res === $pid) {
                    // Задача завершена
                    break;
                }

                sleep(1); // Пауза перед следующей проверкой
            }
        }
    }

    protected function cleanupResources(): void
    {
         gc_collect_cycles();
    }

    abstract protected function handle(): void;
}
