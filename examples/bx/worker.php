<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');

use Cargonomica\Qunique\Queue;

require __DIR__ . "/../prolog.php";

$lowPriorityQueue = new Queue("low");
$middlePriorityQueue = new Queue("middle");
$highPriorityQueue = new Queue("high");



/*

А как сделать так:
```php

Может имеет смысл, например, методы задач сделать дочерними методами методов очереди?

```


 */


