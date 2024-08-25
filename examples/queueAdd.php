<?php

use Qunique\Qunique;

ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');
error_reporting(0); // Отключает все отчёты об ошибках
ini_set('display_errors', '0'); // Отключает отображение ошибок

require __DIR__ . "/../vendor/autoload.php";
//
Qunique::init();

Qunique::push(
    \App\ExampleJob::class,
    ["s" => "text", "a" => 1, "b" => 2,],
    '<#timeout: 3c#>',
    3,
);

Qunique::push(
    \App\ExampleJob::class,
    ["s" => "text", "a" => 1, "b" => 2,],
    '<#timeout: 5c#>',
    5,
);
Qunique::push(
    \App\ExampleJob::class,
    ["s" => "text", "a" => 1, "b" => 2,],
    '<#timeout: 6c#>',
    6,
);

Qunique::push(
    \App\ExampleJob::class,
    ["s" => "text", "a" => 1, "b" => 2,],
    '<#timeout: 10c#>',
    10,
);

Qunique::push(
    \App\ExampleJob::class,
    ["s" => "text", "a" => 1, "b" => 2,],
    '<#timeout: 30c#>',
    30,
);

