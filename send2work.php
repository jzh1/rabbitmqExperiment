<?php
require_once 'RabbitConnection.php';
use PhpAmqpLib\Message\AMQPMessage;

$rabbitConnection = new RabbitConnection();
$channel = $rabbitConnection->getChannel();
const QUEUE_NAME = 'work';

// 建立queue
$channel->queue_declare(QUEUE_NAME, false, false, false, false);

// 发送消息
for ($i = 0; $i < 10; $i++) {
    $msg = new AMQPMessage('Hello World!'.$i);
    $channel->basic_publish($msg, '', QUEUE_NAME);
    echo $i." [x] Sent 'Hello World!'\n";
}

$channel->close();
$rabbitConnection->closeConnection();
