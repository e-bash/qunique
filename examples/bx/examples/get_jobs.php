<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');

use Cargonomica\Qunique\Queue;

require __DIR__ . "/../../prolog.php";

Queue::init();

$startMemory = memory_get_usage();
$jobs = Queue::getJobs(0, 10);
//$jobs = Queue::getJobs(0, 10, ["tag1"]);
$endMemory = memory_get_usage();
echo (($endMemory - $startMemory) / 1024 / 1024) . PHP_EOL;
echo $jobs;



