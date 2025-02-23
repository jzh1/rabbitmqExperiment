<?php
require_once 'RabbitConnection.php';

$rabbitConnection = new RabbitConnection();
$channel = $rabbitConnection->getChannel();
const QUEUE_NAME = 'work';

$channel->queue_declare(QUEUE_NAME, false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";
$callback = function ($msg) {
    echo ' [x] Received 1号', $msg->body, "\n";
};

// 监听队列
$channel->basic_consume(QUEUE_NAME, '', false, true, false, false, $callback);

try {
    $channel->consume();
} catch (\Exception $exception) {
    echo $exception->getMessage();
}
