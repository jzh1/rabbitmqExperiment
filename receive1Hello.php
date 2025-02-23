<?php
require_once 'RabbitConnection.php';

$rabbitConnection = new RabbitConnection();
$channel = $rabbitConnection->getChannel();

$channel->queue_declare('hello', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";
$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
};

// 监听队列
$channel->basic_consume('hello', '', false, true, false, false, $callback);

try {
    $channel->consume();
} catch (\Exception $exception) {
    echo $exception->getMessage();
}
