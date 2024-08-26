<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');

use Cargonomica\Qunique\Job;
use Cargonomica\Qunique\Jobs\ExampleJob;
use Cargonomica\Qunique\Queue;

require __DIR__ . "/../../prolog.php";

$jobs_count = 1000;
$allTags = [
    "tag1",
    "tag2",
    "tag3",
    "tag4",
    "tag5",
    "tag6",
    "tag7",
    "tag8",
    "tag9",
    "tag10",
    "tag11",
    "tag12",
    "tag13",
    "tag14",
];

$lowPriorityQueue = new Queue("low");
$middlePriorityQueue = new Queue("middle");
$highPriorityQueue = new Queue("high");

$startTime = microtime(true);

for ($i = 0; $i < $jobs_count; $i++) {
    $lowJob = new Job();
    $lowJob
        ->setTitle("low task")
        ->setClass(ExampleJob::class)
        ->setArgs(['arg1' => "test", 'arg2' => 2, 'arg3' => 5])
        ->setDelay(0)
        ->setTries(3);
    $numTags = rand(0, count($allTags));
    $selectedTags = [];
    if ($numTags > 0) {
        $selectedTags = array_rand(array_flip($allTags), $numTags);
        if (!is_array($selectedTags)) {
            $selectedTags = [$selectedTags];
        }
    }
    foreach ($selectedTags as $tag) {
        $lowJob->addTag($tag);
    }
    $lowPriorityQueue->pushJob($lowJob);
}

echo PHP_EOL . microtime(true) - $startTime . PHP_EOL;

