# Qunique Library

## Оглавление

- [Общее назначение и принцип работы](#общее-назначение-и-принцип-работы)
- [Методы класса `Qunique`](#методы-класса-qunique)
    - [`init`](#init)
    - [`push`](#push)
    - [`popAndExecute`](#popandexecute)
    - [`handleJobError`](#handleJobError)
    - [`getFailedJobs`](#getfailedjobs)
    - [`retryFailedJobs`](#retryFailedJobs)
    - [`getJobs`](#getjobs)
    - [`clearJobsQueue`](#clearjobsqueue)
    - [`clearFailedJobsQueue`](#clearfailedjobsqueue)
    - [`getAllTags`](#getalltags)
- [Класс `BaseJob`](#базовый-класс-basejob)

## Общее назначение и принцип работы

Библиотека Qunique предназначена для работы с задачами, хранящимися в очереди Redis. Она предоставляет удобный интерфейс
для добавления, выполнения и управления задачами.

### Основные функции библиотеки

1. **Инициализация**: Настройка подключения к Redis и установка очереди задач.
2. **Добавление задач**: Позволяет добавлять задачи в очередь с параметрами, включая название, класс, аргументы и теги.
3. **Выполнение задач**: Извлечение задач из очереди, их выполнение и обработка ошибок.
4. **Управление неудачными задачами**: Позволяет повторно пытаться выполнить неудачные задачи и получать список всех
   неудачных задач.
5. **Получение и очистка очередей**: Функции для получения задач с учетом пагинации и тегов, а также для очистки
   очередей.

### Создание задач

Для того чтобы добавлять задачи в очередь и обрабатывать их, необходимо создать класс-наследник от `BaseJob`. Этот
абстрактный класс содержит метод `execute`, который вызывает абстрактный метод `handle`. Класс-наследник должен
реализовать метод `handle`, где описывается логика выполнения задачи.

Пример создания класса-наследника:

```php
namespace YourNamespace;

use Cargonomica\Qunique\BaseJob;

class MyJob extends BaseJob
{
    protected function handle(): void
    {
        // Логика выполнения задачи
        echo "Задача выполнена.";
    }
}
```

## Методы класса `Qunique`

### `init`

```php
public static function init(string $queue = 'default_queue'): void
```

Инициализирует клиента Redis и устанавливает очередь задач.

#### Параметры

- `string $queue` (необязательно): Название очереди. По умолчанию `'default_queue'`.

#### Возвращаемое значение

- `void`

#### Примеры использования

```php
// Инициализация с использованием очереди по умолчанию
Qunique::init();

// Инициализация с использованием пользовательской очереди
Qunique::init('my_custom_queue');
```

### `push`

```php
public static function push(string $title, string $class, array $args = [], array $tags = [], int $tries = 1, int $delay = 0): string
```

Добавляет задачу в очередь.

#### Параметры

- `string $title`: Название задачи, поддерживающее форматирование Markdown.
- `string $class`: Имя класса задачи, который должен быть вызван.
- `array $args` (необязательно): Массив аргументов для выполнения задачи.
- `array $tags` (необязательно): Массив тегов для задачи.
- `int $tries` (необязательно): Количество попыток выполнения задачи. По умолчанию `1`.
- `int $delay` (необязательно): Задержка перед выполнением задачи (в секундах). По умолчанию `0`.

#### Возвращаемое значение

- `string`: Уникальный идентификатор задачи.

#### Примеры использования

```php
// Добавление задачи с названием и классом
$id = Qunique::push('Моя задача', 'YourNamespace\MyJob');

// Добавление задачи с аргументами и тегами
$id = Qunique::push('Задача с аргументами', 'YourNamespace\MyJob', ['arg1', 'arg2'], ['tag1', 'tag2'], 3, 10);
```

### `popAndExecute`

```php
public static function popAndExecute(): ?array
```

Извлекает задачу из очереди и выполняет её.

#### Возвращаемое значение

- `array|null`: Информация о результате выполнения задачи, или `null`, если задача не готова к выполнению.

#### Примеры использования

```php
// Извлечение и выполнение задачи
$result = Qunique::popAndExecute();
if ($result) {
    echo "Задача {$result['id']} выполнена успешно.";
} else {
    echo "Задача не готова к выполнению.";
}
```

### `handleJobError`

```php
private static function handleJobError(array &$jobData, Throwable $e): void
```

Обрабатывает ошибки выполнения задачи.

#### Параметры

- `array &$jobData`: Данные о задаче.
- `Throwable $e`: Исключение, которое было выброшено.

#### Возвращаемое значение

- `void`

### `getFailedJobs`

```php
public static function getFailedJobs(): array
```

Получает список задач, которые не удалось выполнить.

#### Возвращаемое значение

- `array`: Массив данных неудачных задач.

#### Примеры использования

```php
// Получение списка неудачных задач
$failedJobs = Qunique::getFailedJobs();
foreach ($failedJobs as $job) {
    echo "Неудачная задача: {$job['id']}";
}
```

### `retryFailedJobs`

```php
public static function retryFailedJobs(): void
```

Повторно пытается выполнить неудачные задачи.

#### Возвращаемое значение

- `void`

#### Примеры использования

```php
// Попытка повторного выполнения неудачных задач
Qunique::retryFailedJobs();
```

### `getJobs`

```php
public static function getJobs(int $page = 0, int $limit = 500, array $tags = []): string
```

Получает список задач с учетом пагинации и тегов.

#### Параметры

- `int $page` (необязательно): Номер страницы. По умолчанию `0`.
- `int $limit` (необязательно): Количество задач на странице. По умолчанию `500`.
- `array $tags` (необязательно): Массив тегов для фильтрации задач.

#### Возвращаемое значение

- `string`: JSON-строка с задачами.

#### Примеры использования

```php
// Получение задач без фильтрации
$jobs = Qunique::getJobs();

// Получение задач с фильтрацией по тегам
$jobs = Qunique::getJobs(1, 100, ['tag1']);
```

### `clearJobsQueue`

```php
public static function clearJobsQueue(): void
```

Очищает очередь задач.

#### Возвращаемое значение

- `void`

#### Примеры использования

```php
// Очистка очереди задач
Qunique::clearJobsQueue();
```

### `clearFailedJobsQueue`

```php
public static function clearFailedJobsQueue(): void
```

Очищает очередь неудачных задач.

#### Возвращаемое значение

- `void`

#### Примеры использования

```php
// Очистка очереди неудачных задач
Qunique::clearFailedJobsQueue();
```

### `getAllTags`

```php
public static function getAllTags(): array
```

Получает список всех тегов.

#### Возвращаемое значение

- `array`: Массив всех тегов.

#### Примеры использования

```php
// Получение всех тегов
$tags = Qunique::getAllTags();
foreach ($tags as $tag) {
    echo "Тег: $tag";
}
```

## Класс `BaseJob`

```php
<?php

namespace Cargonomica\Qunique;

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
```

### Общее назначение

`BaseJob` — это абстрактный класс, который следует расширить для создания конкретных задач. Он предоставляет метод
`execute`, который автоматически вызывает метод `handle`, содержащий логику выполнения задачи. Время начала и окончания
выполнения задачи сохраняется в свойствах `startTime` и `endTime`.

### Пример создания задачи

```php
namespace YourNamespace;

use Cargonomica\Qunique\BaseJob;

class MyJob extends BaseJob
{
    protected function handle(): void
    {
        // Логика выполнения задачи
        echo "Задача выполнена.";
    }
}
```

Создание задачи и её выполнение:

```php
$jobId = Qunique::push('Example Job', 'YourNamespace\MyJob

');
$result = Qunique::popAndExecute();
if ($result) {
    echo "Задача {$result['id']} выполнена успешно.";
}
```
