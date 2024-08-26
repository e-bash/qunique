<?php

namespace Cargonomica\Qunique\Jobs;

use Cargonomica\Qunique\BaseJob;
use Random\RandomException;

/**
 * Пример класса задачи, который добавляется в очередь.
 */
class ExampleJob extends BaseJob
{
    protected mixed $arg1;
    protected mixed $arg2;
    protected mixed $arg3;

    public function __construct(mixed $arg1, mixed $arg2, mixed $arg3)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
        $this->arg3 = $arg3;
    }

    /**
     * @throws RandomException
     */
    protected function handle(): void
    {
        $this->exampleTaskMethod();
    }

    /**
     * @throws RandomException
     */
    private function exampleTaskMethod(): void
    {
        $count = random_int(1, 100);
        for ($i = 0; $i < $count; $i++) {
            echo $i . "/" . $count . $this->arg1 . " - " . ($this->arg2 + $this->arg3) . PHP_EOL;
            usleep(10000);
            ob_flush();
            flush();
        }
    }
}
