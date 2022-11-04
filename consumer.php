<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\QueueHelper;
use PhpAmqpLib\Connection\AMQPStreamConnection;

if (empty($argv[1])) {
    exit('Queue argument is missing');
} elseif (!in_array($argv[1], [QueueHelper::QUEUE_ODD, QueueHelper::QUEUE_EVEN])) {
    exit('Wrong queue name');
}

$queueName = $argv[1];

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare($queueName, false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(1);
};

$channel->basic_consume($queueName, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
