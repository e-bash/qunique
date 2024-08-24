# Qunique: Инструкция по установке и использованию

## Описание

**Qunique** — это мощный фреймворк управления очередями задач для PHP и Redis. Он позволяет легко создавать, управлять и выполнять задачи в асинхронном режиме, используя Redis как хранилище очередей.

## Установка

### Шаг 1: Установка через Composer

1. **Добавьте библиотеку в ваш проект с помощью Composer**:

   ```bash
   composer require suprunov/qunique
   ```

2. **Убедитесь, что у вас установлены необходимые расширения PHP**:
    - `ext-pcntl`
    - `ext-posix`
    - `ext-json`

   Установите их через ваш менеджер пакетов или `php.ini`, если они ещё не установлены.

### Шаг 2: Конфигурация Redis

1. **Убедитесь, что Redis установлен и запущен**. Вы можете скачать Redis с [официального сайта](https://redis.io/download) и следовать инструкциям по установке.

2. **Настройте Redis**:
    - По умолчанию, Qunique подключается к Redis на `localhost` и порту `6379`. Вы можете изменить это в конфигурации вашего Redis клиента, если нужно.

## Использование

### 1. Инициализация и добавление задач

Для инициализации и добавления задач в очередь выполните следующие шаги:

1. **Создайте скрипт для инициализации очереди и добавления задач**:

   ```php
   <?php

   use Qunique\Qunique;

   require __DIR__ . "/../vendor/autoload.php";

   // Инициализация очереди
   Qunique::init();

   // Добавление задач
   Qunique::push(
       \App\ExampleJob::class,
       ["s" => "text", "a" => 1, "b" => 2],
       '<#timeout: 3c#>',
       3
   );

   Qunique::push(
       \App\ExampleJob::class,
       ["s" => "text", "a" => 1, "b" => 2],
       '<#timeout: 5c#>',
       5
   );
   ```

2. **Пример задачи**:

   ```php
   <?php

   namespace App;

   use Qunique\BaseJob;

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
           // Код выполнения задачи
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
   ```

### 2. Выполнение задач

Для выполнения задач из очереди используйте следующий скрипт:

1. **Создайте скрипт для выполнения задач**:

   ```php
   <?php

   use Qunique\Qunique;

   require __DIR__ . "/../vendor/autoload.php";

   // Инициализация очереди
   Qunique::init();

   // Выполнение задач
   Qunique::popAndExecute();
   ```

### 3. Управление неудачными задачами

Для получения и повторной попытки выполнения неудачных задач, используйте следующие методы:

1. **Получение неудачных задач**:

   ```php
   <?php

   use Qunique\Qunique;

   require __DIR__ . "/../vendor/autoload.php";

   // Инициализация очереди
   Qunique::init();

   // Получение неудачных задач
   $failedJobs = Qunique::getFailedJobs();
   print_r($failedJobs);
   ```

2. **Повторная попытка выполнения неудачных задач**:

   ```php
   <?php

   use Qunique\Qunique;

   require __DIR__ . "/../vendor/autoload.php";

   // Инициализация очереди
   Qunique::init();

   // Повторная попытка выполнения неудачных задач
   Qunique::retryFailedJobs();
   ```

## Примеры использования

### Пример добавления задачи с заданным таймаутом:

```php
Qunique::push(
    \App\ExampleJob::class,
    ["s" => "text", "a" => 1, "b" => 2],
    '<#timeout: 10c#>',
    10
);
```

### Пример выполнения задач:

```php
Qunique::popAndExecute();
```

## Замечания

- Убедитесь, что Redis сервер работает и доступен по умолчанию на `localhost:6379`.
- Использование `pcntl_fork` требует CLI режим работы PHP, так как он не поддерживается в веб-сервере (например, Apache или Nginx).

## Контрибьюция

Если у вас есть предложения или ошибки, пожалуйста, создайте [issue](https://github.com/suprunov/qunique/issues) в репозитории или отправьте пулл-реквест.

## Лицензия

Этот проект лицензирован под [MIT License](https://opensource.org/licenses/MIT).
