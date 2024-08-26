<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');

use Cargonomica\Qunique\Queue;

require __DIR__ . "/../../prolog.php";

Queue::init();

Queue::retryFailedJobs();


