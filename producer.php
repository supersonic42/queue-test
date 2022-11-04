<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\QueueHelper;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare(QueueHelper::QUEUE_ODD, false, false, false, false);
$channel->queue_declare(QueueHelper::QUEUE_EVEN, false, false, false, false);

$eventCount = 10000;
$userCount = 1000;
$eventId = 0;

/**
 * Эмуляция получения масовых сообщений
 */
while ($eventCount) {
    $eventId++;

    $data = [
        'user_id' => rand(1, $userCount),
        'event_id' => $eventId,
    ];

    $queueName = $data['user_id'] % 2 == 0
        ? QueueHelper::QUEUE_EVEN
        : QueueHelper::QUEUE_ODD;

    $queueNameFormatted = str_pad($queueName, max(strlen(QueueHelper::QUEUE_EVEN), strlen(QueueHelper::QUEUE_ODD)), ' ');

    $msgStr = json_encode($data);
    $msg = new AMQPMessage($msgStr);
    $channel->basic_publish($msg, '', $queueName);

    echo " [x] Sent | Queue: {$queueNameFormatted} | {$msgStr}\n";

    $eventCount--;
}

$channel->close();
$connection->close();
