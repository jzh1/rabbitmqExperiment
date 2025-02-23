<?php
require_once 'RabbitConnection.php';
use PhpAmqpLib\Message\AMQPMessage;

$rabbitConnection = new RabbitConnection();
$channel = $rabbitConnection->getChannel();

// 建立queue
$channel->queue_declare('hello', false, false, false, false);

// 发送消息
$msg = new AMQPMessage('Hello World!');
$channel->basic_publish($msg, '', 'hello');

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$rabbitConnection->closeConnection();
