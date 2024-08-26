<?php

namespace Qunique;

/**
 * Абстрактный класс для задач, которые добавляются в очередь.
 */
abstract class BaseJob
{
    protected int $startTime;
    protected int $endTime;

    public function execute(): void
    {
        $this->startTime = time();
        $this->handle();
        $this->endTime = time();
    }

    abstract protected function handle(): void;
}
