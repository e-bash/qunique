В системе очередей, подобной той, что мы реализовали, при добавлении задачи в очередь передаются несколько параметров,
которые определяют, что это за задача, как и когда она должна быть выполнена. Давайте рассмотрим каждый из этих
аргументов подробнее.

### Аргументы метода `push`

```php
public static function push(
    string $class, 
    string $method, 
    array $args = [], 
    int $delay = 0, 
    int $tries = 1, 
    ?int $timeout = null
): string
```

#### 1. **`$class` (string)**

- **Описание**: Имя класса, который реализует задачу.
- **Пример**: Если у вас есть класс `AddNumbersJob`, который реализует метод `handle`, то при добавлении задачи в
  очередь вы укажете `AddNumbersJob::class`.
- **Назначение**: Этот параметр указывает системе очередей, какой класс будет создан для выполнения задачи. Это
  позволяет задаче быть гибкой и поддерживать любое количество типов задач.

#### 2. **`$method` (string)**

- **Описание**: Имя метода, который будет вызван у экземпляра указанного класса.
- **Пример**: Если класс `AddNumbersJob` имеет метод `handle`, который выполняет основную работу, то вы указываете
  `'handle'` в этом параметре.
- **Назначение**: Этот параметр позволяет вам выбирать, какой именно метод будет вызван при выполнении задачи. Это дает
  возможность выполнять разные методы внутри одного и того же класса.

#### 3. **`$args` (array)**

- **Описание**: Массив аргументов, которые будут переданы в метод задачи при её выполнении.
- **Пример**: Если метод `handle` в `AddNumbersJob` ожидает два аргумента для сложения, вы передаете их в массиве,
  например, `[5, 10]`.
- **Назначение**: Эти аргументы позволяют передать данные, необходимые для выполнения задачи. Это могут быть любые
  данные, которые метод должен обработать.

#### 4. **`$delay` (int)**

- **Описание**: Время задержки перед выполнением задачи в секундах.
- **Пример**: Если вы хотите, чтобы задача выполнялась через 10 минут после добавления в очередь, укажите `600` (10
  минут * 60 секунд).
- **Назначение**: Это полезно, если нужно отложить выполнение задачи на определенное время, например, для выполнения
  задач по расписанию или для минимизации нагрузки на систему.

#### 5. **`$tries` (int)**

- **Описание**: Количество попыток выполнения задачи в случае её неудачи.
- **Пример**: Если задача должна быть повторена до 3 раз в случае неудачи, укажите `3`.
- **Назначение**: Этот параметр позволяет системе попытаться выполнить задачу несколько раз, если она не удалась с
  первой попытки. Это полезно для задач, которые могут временно не выполняться из-за внешних факторов (например, сетевых
  ошибок).

#### 6. **`$timeout` (int|null)**

- **Описание**: Максимальное время (в секундах), за которое задача должна быть выполнена.
- **Пример**: Если задача должна завершиться в течение 30 секунд, укажите `30`.
- **Назначение**: Этот параметр используется для ограничения времени выполнения задачи, чтобы избежать зависания системы
  на слишком долго выполняющихся задачах. Если задача не завершится в течение указанного времени, она будет
  принудительно завершена.

### Пример использования метода `push`

Предположим, у вас есть задача, которая складывает два числа и должна быть выполнена через 5 минут, с максимальным
временем выполнения 60 секунд и 3 попытками на случай неудачи:

```php
QueueManager::push(
    AddNumbersJob::class, // Класс задачи
    'handle',             // Метод для выполнения
    [5, 10],              // Аргументы для метода
    300,                  // Задержка 5 минут (300 секунд)
    3,                    // 3 попытки
    60                    // Таймаут 60 секунд
);
```

### Взаимодействие параметров

- **`$class` и `$method`**: Эти параметры вместе определяют, какой код будет выполнен, что позволяет легко управлять
  разными типами задач в одной системе очередей.
- **`$args`**: Позволяют гибко передавать данные для выполнения задачи, что делает систему очень универсальной.
- **`$delay`**: Удобно для планирования задач на будущее, когда выполнение не должно происходить немедленно.
- **`$tries` и `$timeout`**: Эти параметры помогают управлять задачами, которые могут столкнуться с проблемами при
  выполнении, обеспечивая надёжное выполнение критических задач.

Эта система позволяет легко управлять выполнением задач, обеспечивая гибкость и контроль над процессом выполнения.



------------------------------------------------------------------------------------

Система очередей, которую мы разработали, поддерживает не только статические методы классов, но и динамические методы,
безымянные функции (анонимные функции или замыкания), а также методы классов с конструкторами. Однако, есть несколько
нюансов, связанных с передачей таких задач в очередь.

### 1. **Безымянные функции (анонимные функции или замыкания)**

Анонимные функции и замыкания не могут быть сериализованы напрямую с использованием стандартного механизма сериализации
в PHP, поэтому их нельзя легко положить в очередь для последующего выполнения.

### Решение:

- Если вам нужно использовать замыкания, вы можете использовать библиотеку **opis/closure** для сериализации и
  десериализации замыканий. Эта библиотека позволяет вам передавать замыкания через очередь.
- Альтернативно, можно обернуть такие функции в специальные классы (job-классы), которые могут быть сериализованы.

### Пример с opis/closure:

```php
use Opis\Closure\SerializableClosure;

$job = new SerializableClosure(function() {
    echo 'Hello, world!';
});
QueueManager::push($job);
```

### 2. **Методы класса с конструктором**

Если нужно вызвать метод экземпляра класса, который должен быть создан с параметрами в конструкторе, можно передать имя
класса, метод и аргументы, которые нужны для создания объекта и вызова метода.

### Пример:

```php
// Передаем класс, метод, аргументы для конструктора и аргументы для метода
QueueManager::push(
    Cargonomica\Services\Contacts::class,
    'update',
    ['constructorArg1' => 123, 'methodArg' => 456],
    0, // delay
    1, // tries
    null, // timeout
    null // taskTitle
);
```

### 3. **Как это работает в системе**

- **Статические методы**: Поддерживаются без изменений. Просто указываете класс и метод.

- **Динамические методы (методы экземпляра класса)**: Вы указываете класс, метод и аргументы. В методе выполнения задачи
  экземпляр класса создается с использованием `new`, и затем вызывается указанный метод.

- **Анонимные функции**: Для работы с анонимными функциями необходима сериализация с помощью сторонней библиотеки, такой
  как **opis/closure**.

### Обновлённый код для работы с динамическими методами и конструкторами

Вместо передачи имени класса и метода, можно передать уже готовый объект:

```php
<?php

namespace Cargonomica\Service\Queue;

use Exception;
use Predis\Client;
use Opis\Closure\SerializableClosure;

class QueueManager
{
    protected static Client $redis;
    protected static mixed $queue;
    protected static string $failedQueue;

    public static function init($queue = 'default_queue'): void
    {
        self::$redis = new Client();
        self::$queue = $queue;
        self::$failedQueue = $queue . '_failed';
    }

    public static function push(
        callable $task,
        array $args = [],
        int $delay = 0,
        int $tries = 1,
        ?int $timeout = null,
        ?string $taskTitle = null
    ): string {
        // Если taskTitle начинается и заканчивается на "!!", используем его без изменений
        if ($taskTitle && str_starts_with($taskTitle, '!!') && str_ends_with($taskTitle, '!!')) {
            $taskTitle = trim($taskTitle, '!!');
        } else {
            // Автоматическое формирование taskTitle
            if (!$taskTitle) {
                $taskTitle = '*Безымянная задача*';
            }
            $taskTitle .= " (Вызов метода `{$task}` с аргументами `{$args}`)";
        }

        // Подготовка данных задачи
        $jobData = [
            'task' => new SerializableClosure($task),
            'args' => $args,
            'tries' => $tries,
            'timeout' => $timeout,
            'delay' => $delay,
            'id' => uniqid('', true),
            'taskTitle' => $taskTitle,
            'added_at' => time()
        ];

        // Сериализуем данные задачи и добавляем их в очередь
        self::$redis->rpush(self::$queue, json_encode($jobData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $jobData['id'];
    }

    public static function popAndExecute(): ?array
    {
        $data = self::$redis->lpop(self::$queue);
        if ($data) {
            $jobData = json_decode($data, true);
            $current_time = time();

            if ($current_time - $jobData['added_at'] < $jobData['delay']) {
                self::$redis->rpush(self::$queue, json_encode($jobData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                return null;
            }

            $jobData['tries']--;

            try {
                $task = $jobData['task'];
                $args = $jobData['args'];

                $result = $task(...$args);

                return ['id' => $jobData['id'], 'status' => 'success'];
            } catch (Exception $e) {
                if ($jobData['tries'] > 0) {
                    self::$redis->rpush(self::$queue, json_encode($jobData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                } else {
                    self::$redis->rpush(self::$failedQueue, json_encode($jobData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    return ['id' => $jobData['id'], 'status' => 'failed', 'error' => $e->getMessage()];
                }
            }
        }
        return null;
    }

    public static function getFailedJobs(): array
    {
        $failedJobs = self::$redis->lrange(self::$failedQueue, 0, -1);
        return array_map(function ($job) {
            return json_decode($job, true);
        }, $failedJobs);
    }

    public static function retryFailedJobs(): void
    {
        $failedJobs = self::getFailedJobs();
        foreach ($failedJobs as $job) {
            self::push($job['task'], $job['args'], 0, $job['tries'], $job['timeout'], $job['taskTitle']);
        }
        self::$redis->del(self::$failedQueue);
    }
}
```

### Заключение

Этот подход позволяет передавать в очередь более сложные задачи, включая анонимные функции и методы экземпляров классов
с конструкторами. Благодаря использованию библиотеки **opis/closure**, вы можете сериализовать и десериализовать
анонимные функции, что делает систему очередей ещё более гибкой и мощной.

```php
<?php
//queueAdd.php
ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');

use Cargonomica\Service\Queue\AddNumbersJob;
use Cargonomica\Service\Queue\AddStringsJob;
use Cargonomica\Service\Queue\Qunique;

require __DIR__ . "/prolog.php";

// Инициализация очереди
Qunique::init();

// Добавление задачи в очередь
$taskId = Qunique::push(AddNumbersJob::class, 'handle', [1, 2]);
echo print_r($taskId, 1) . PHP_EOL;
Qunique::push(AddNumbersJob::class, 'handle', [15, 20], 0, 1, null, '_Обновление элементов сущности *Контакт*_');
Qunique::push(AddNumbersJob::class, 'handle', [2, 4], 0, 1, null, '!!Неизменяемое название задачи!!');
Qunique::push(AddStringsJob::class, 'handle', ["s" => "text", "a" => 1, "b" => 2,], 0, 1, null, '!!Неизменяемое название задачи!!');
```

```php
//queueRun.php
<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');

use Cargonomica\Service\Queue\Qunique;

require __DIR__ . "/prolog.php";

// Инициализация очереди
Qunique::init();

// Извлечение и выполнение задачи
Qunique::popAndExecute();
```

```php
<?php
namespace Cargonomica\Service\Queue;

/**
 * Пример класса задачи, который добавляется в очередь.
 * В данном случае задача заключается в сложении двух чисел.
 */
class AddNumbersJob
{
    protected $a;
    protected $b;

    /**
     * Конструктор задачи.
     *
     * @param mixed $a Первое число.
     * @param mixed $b Второе число.
     */
    public function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * Метод, который выполняет задачу.
     */
    public function handle(): void
    {
        // Простая задача сложения двух чисел и вывода результата
        echo $this->a + $this->b . "\n";
    }
}
```

```php
<?php

namespace Cargonomica\Service\Queue;

use AllowDynamicProperties;

/**
 * Пример класса задачи, который добавляется в очередь.
 * В данном случае задача заключается в сложении двух чисел.
 */
#[AllowDynamicProperties] class AddStringsJob
{
    protected $a;
    protected $b;
    protected $s;

    /**
     * Конструктор задачи.
     *
     * @param mixed $a Первое число.
     * @param mixed $b Второе число.
     * @param mixed $s Второе число.
     */
    public function __construct($a, $b, $s)
    {
        $this->a = $a;
        $this->b = $b;
        $this->s = $s;
    }

    /**
     * Метод, который выполняет задачу.
     */
    public function handle(): void
    {
        // Простая задача сложения двух чисел и вывода результата
        echo $this->s . ": " . ($this->a + $this->b) . "\n";
    }

}

```