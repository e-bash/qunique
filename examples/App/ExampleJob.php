<?php

namespace App;

use Qunique\BaseJob;

/**
 * Пример класса задачи, который добавляется в очередь.
 */
class ExampleJob extends BaseJob
{
    protected mixed $a;
    protected mixed $b;
    protected mixed $s;

    public function __construct(mixed $a, mixed $b, mixed $s)
    {
        parent::__construct(); // Инициализируем родительский конструктор
        $this->a = $a;
        $this->b = $b;
        $this->s = $s;
    }

    protected function handle(): void
    {
        // Код выполнения задачи, который не обязательно должен содержать частые проверки таймаута
        $this->simulateLongTask();
    }

    private function simulateLongTask(): void
    {
        // Долгий процесс, который нужно прервать по таймауту
        for ($i = 0; $i < 12; $i++) {
            echo $i . "c: " . $this->s . " - " . ($this->a + $this->b) . PHP_EOL;
            sleep(1);
        }
    }
}
