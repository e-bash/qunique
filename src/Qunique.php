<?php

namespace Qunique;

use Exception;
use Predis\Client;

class Qunique
{
    protected static Client $redis;
    protected static mixed $queue;
    protected static string $failedQueue;

    /**
     * Инициализация клиента Redis и установка очереди.
     *
     * @param string $queue Название очереди, по умолчанию 'default_queue'.
     */
    public static function init(string $queue = 'default_queue'): void
    {
        self::$redis = new Client();
        self::$queue = $queue;
        self::$failedQueue = $queue . '_failed';
    }

    /**
     * Добавление задачи в очередь.
     *
     * @param string $class Имя класса задачи, который должен быть вызван.
     * @param array $args Массив аргументов для выполнения задачи.
     * @param int $delay Задержка перед выполнением задачи (в секундах).
     * @param int $tries Количество попыток выполнения задачи.
     * @param int|null $timeout Максимальное время выполнения задачи (в секундах).
     * @param string|null $taskTitle Название задачи, поддерживающее форматирование Markdown.
     * @return string Возвращает уникальный идентификатор задачи.
     */
    public static function push(
        string  $class,
        array   $args = [],
        ?string $taskTitle = null,
        ?int    $timeout = null,
        int     $tries = 1,
        int     $delay = 0,
    ): string
    {
        // Формируем строку аргументов в формате JSON
        $argsJson = json_encode($args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Если taskTitle начинается и заканчивается на "!!", используем его без изменений
        if ($taskTitle && str_starts_with(trim($taskTitle), '<#') && str_ends_with(trim($taskTitle), '#>')) {
            // Убираем символы экранирования с начала и конца строки
            $taskTitle = trim(ltrim(rtrim(trim($taskTitle), '#>'), '<#'));
        } else {
            // Если taskTitle не задано, формируем его автоматически
            if (!$taskTitle) {
                $taskTitle = '*Безымянная задача*';
            }
            // Дополняем заголовок информацией о вызове метода и аргументах
            $taskTitle .= " (Класс задачи`{$class}` с аргументами `{$argsJson}`)";
        }

        // Подготовка данных для задачи в формате JSON
        $jobData = [
            'title' => $taskTitle, // Добавляем taskTitle в данные задачи
            'id' => uniqid('', true),
            'class' => $class,
            'args' => $args,
            'tries' => $tries,
            'timeout' => $timeout,
            'delay' => $delay,
            'added_at' => time()
        ];

        // Добавление задачи в очередь в формате JSON
        self::$redis->rpush(self::$queue, (array)json_encode($jobData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $jobData['id'];
    }

    /**
     * Извлечение задачи из очереди и её выполнение.
     *
     * @return array|null Возвращает информацию о результате выполнения задачи.
     */

    public static function popAndExecute(): ?array
    {
        $data = self::$redis->lpop(self::$queue);
        if ($data) {
            $jobData = json_decode($data, true);
            $current_time = time();

            // Если задержка ещё не прошла, помещаем задачу обратно в очередь
            if ($current_time - $jobData['added_at'] < $jobData['delay']) {
                self::$redis->rpush(self::$queue, (array)json_encode($jobData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                return null;
            }

            // Сохраняем время начала выполнения задачи
            $start_time = time();
            $timeout = $jobData['timeout'] ?? 0; // Используем значение тайм-аута из данных задачи

            try {
                $class = $jobData['class'];

                // Вызов метода класса с регулярной проверкой времени
                $object = new $class(...$jobData['args']);
                $object->execute($timeout); // Передаем тайм-аут в метод обработки, если метод поддерживает

                // Проверяем, превышен ли таймаут
                if ((time() - $start_time) > $timeout && $timeout > 0) {
                    throw new \RuntimeException('Task execution exceeded the timeout limit.');
                }

                // Если задача выполнена успешно
                return ['id' => $jobData['id'], 'status' => 'success'];

            } catch (\DivisionByZeroError $e) {
                $errorMessage = "Division by zero: " . $e->getMessage();
            } catch (\TypeError $e) {
                $errorMessage = "Type error: " . $e->getMessage();
            } catch (\ParseError $e) {
                $errorMessage = "Parse error: " . $e->getMessage();
            } catch (\AssertionError $e) {
                $errorMessage = "Assertion error: " . $e->getMessage();
            } catch (\RuntimeException $e) {
                $errorMessage = "Runtime error: " . $e->getMessage();
            } catch (\Throwable $e) {
                $errorMessage = "General error: " . $e->getMessage();
            }

            // Если произошла ошибка, и это было обработано
            if (isset($errorMessage)) {
                // Добавляем информацию об ошибке к задаче
                $jobData['error_message'] = $errorMessage;

                // Если попытки еще остались, уменьшаем их количество и помещаем задачу обратно в очередь
                if ($jobData['tries'] > 1) {
                    $jobData['tries']--;
                    self::$redis->rpush(self::$queue, (array)json_encode($jobData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                } else {
                    // Если попыток больше не осталось, перемещаем задачу в очередь неудачных задач
                    self::$redis->rpush(self::$failedQueue, (array)json_encode($jobData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    return ['id' => $jobData['id'], 'status' => 'failed', 'error' => $errorMessage];
                }
            }
        }
        return null;
    }


    /**
     * Получение списка задач, которые не удалось выполнить.
     *
     * @return array Возвращает массив данных неудачных задач.
     */
    public static function getFailedJobs(): array
    {
        $failedJobs = self::$redis->lrange(self::$failedQueue, 0, -1);
        return array_map(function ($job) {
            return json_decode($job, true);
        }, $failedJobs);
    }

    /**
     * Повторная попытка выполнения неудачных задач.
     */
    public static function retryFailedJobs(): void
    {
        $failedJobs = self::getFailedJobs();
        foreach ($failedJobs as $job) {
            self::push($job['class'], $job['method'], $job['args'], 0, $job['tries'], $job['timeout'], $job['taskTitle']);
        }
        self::$redis->del(self::$failedQueue);
    }
}
