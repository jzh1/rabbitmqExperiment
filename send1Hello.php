<?php
require_once 'RabbitConnection.php';
use PhpAmqpLib\Message\AMQPMessage;

$rabbitConnection = new RabbitConnection();
$channel = $rabbitConnection->getChannel();
const QUEUE_NAME = 'hello';

// 建立queue
$channel->queue_declare(QUEUE_NAME, false, false, false, false);

// 发送消息
$msg = new AMQPMessage('Hello World!');
$channel->basic_publish($msg, '', QUEUE_NAME);

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$rabbitConnection->closeConnection();
