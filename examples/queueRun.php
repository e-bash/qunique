<?php

use Qunique\Qunique;

ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');
error_reporting(0); // Отключает все отчёты об ошибках
ini_set('display_errors', '0'); // Отключает отображение ошибок

require __DIR__ . "/../vendor/autoload.php";
// Инициализация очереди
Qunique::init();
// Выполнение задачи
Qunique::popAndExecute();
