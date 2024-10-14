RU:

**Qunique** — это библиотека с открытым исходным кодом, позволяющая быстро и без лишних усилий реализовать в вашем приложении мощный функционал управления очередями задач на основе `Redis`. Она поддерживает интеграцию с проектами на `PHP` и `Node.js`, предоставляя разработчикам все необходимые методы для эффективного управления задачами в очередях.

*Qunique* позволяет легко расширять функциональность, создавая собственные плагины или используя расширения от независимых разработчиков. Вот краткий обзор некоторых популярных плагинов:

- **Альтернативные драйверы хранения**: Замените `Redis` на другие базы данных, такие как `MySQL`, `PostgreSQL` и другие, для хранения задач.
- **REST API и GraphQL**: Управляйте очередями в приложении через удобный API или с использованием возможностей GraphQL.
- **Панель управления**: Административный интерфейс, разработанный как одностраничное приложение (SPA) на React.js, легко интегрируется в любой HTML-код и предоставляет удобные средства управления.

Название "Qunique" — это игра слов, сочетающая "queue" (очередь) и "unique" (уникальный), подчеркивая уникальные возможности в управлении задачами. Лозунг "Queues are simple, tasks are fast!" — "Очереди — просто, задачи — быстро!" — отражает упрощение процесса разработки и повышение эффективности работы с очередями.

Вот список некоторых возможностей библиотеки **Qunique** с краткими описаниями:

1. **Гибкое управление очередями**:
   - Создание, обновление и удаление задач в очереди с легкостью, а также управление их приоритетами.

2. **Поддержка задержек и таймаутов**:
   - Установка задержек и таймаутов для задач, что позволяет эффективно управлять временем выполнения.

3. **Поддержка отложенных задач**:
   - Планирование выполнения задач на определенное время или после выполнения других задач.

4. **Мониторинг и управление**:
   - Встроенные средства для отслеживания состояния очередей и задач, что помогает контролировать производительность и выявлять проблемы.

5. **Автоочистка очередей**:
   - Настройка автоматической очистки завершенных или устаревших задач для поддержания порядка в очередях.

6. **Интеграция с уведомлениями**:
   - Поддержка отправки уведомлений о состоянии задач (успех, ошибка и т.д.) через различные каналы, такие как Email, WebSocket и другие.

7. **Расширяемая архитектура**:
   - Легкая интеграция дополнительных модулей и плагинов для кастомизации функционала библиотеки под конкретные нужды проекта.

8. **Поддержка различных форматов данных**:
   - Работа с различными типами данных в задачах, включая JSON, XML и другие.

9. **Поддержка параллельной обработки**:
   - Возможность обрабатывать несколько задач одновременно для повышения производительности.

10. **Документация и примеры**:
    - Полная документация с примерами использования, что упрощает процесс интеграции и обучения разработчиков.

Эти возможности делают **Qunique** мощным инструментом для управления задачами в приложениях, основанных на `Redis`, и обеспечивают гибкость и эффективность при работе с очередями.

---------------------------------------------------------------
---------------------------------------------------------------

EN:

**Qunique** is an open-source library that allows you to quickly and effortlessly implement powerful task queue management functionality in your application using `Redis`. It supports integration with projects on `PHP` and `Node.js`, providing developers with all the necessary methods for efficient task queue management.

*Qunique* makes it easy to expand functionality by creating custom plugins or using extensions from independent developers. Here’s a brief overview of some popular plugins:

- **Alternative Storage Drivers**: Replace `Redis` with other databases, such as `MySQL`, `PostgreSQL`, and others, for task storage.
- **REST API and GraphQL**: Manage queues in your application via an intuitive API or with modern GraphQL capabilities.
- **Control Panel**: An administrative interface built as a React.js single-page application (SPA) that easily integrates into any HTML code, providing convenient management tools.

The name "Qunique" is a play on the words "queue" and "unique," highlighting its unique capabilities in task management. The slogan "Queues are simple, tasks are fast!" reflects the library's simplicity in development and its enhanced efficiency in task queue management.

Here is a list of some features of the **Qunique** library, with brief descriptions:

1. **Flexible Queue Management**:
   - Easily create, update, and delete tasks in the queue, as well as manage their priorities.

2. **Support for Delays and Timeouts**:
   - Set delays and timeouts for tasks, allowing efficient time management.

3. **Support for Deferred Tasks**:
   - Schedule tasks to be executed at a specific time or after the completion of other tasks.

4. **Monitoring and Management**:
   - Built-in tools for tracking the status of queues and tasks, helping to monitor performance and identify issues.

5. **Queue Auto-Cleanup**:
   - Configure automatic cleanup of completed or outdated tasks to keep queues organized.

6. **Integration with Notifications**:
   - Support for sending task status notifications (success, error, etc.) through various channels, such as Email, WebSocket, and others.

7. **Extensible Architecture**:
   - Easily integrate additional modules and plugins to customize the library’s functionality to fit specific project needs.

8. **Support for Various Data Formats**:
   - Work with different data types in tasks, including JSON, XML, and others.

9. **Support for Parallel Processing**:
   - Process multiple tasks simultaneously to enhance performance.

10. **Documentation and Examples**:
    - Complete documentation with usage examples, simplifying the integration and learning process for developers.

These features make **Qunique** a powerful tool for task management in applications based on `Redis`, providing flexibility and efficiency when working with queues.


---------------------------------------------------------------
---------------------------------------------------------------

# Инструкция по установке и использованию

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
