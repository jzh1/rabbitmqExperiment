<?php
require_once 'RabbitConnection.php';
use PhpAmqpLib\Message\AMQPMessage;

const EXCHANGE_NAME = 'exchange-pub';
const QUEUE_NAME1 = 'pub1';
const QUEUE_NAME2 = 'pub2';

// 1、获取链接对象
$rabbitConnection = new RabbitConnection();
// 2、构建channel
$channel = $rabbitConnection->getChannel();
// 3、构建交换机
$channel->exchange_declare(EXCHANGE_NAME, 'fanout', false, false, false);
// 4、构建queue
$channel->queue_declare(QUEUE_NAME1, false, false, false, false);
$channel->queue_declare(QUEUE_NAME2, false, false, false, false);
// 5、 绑定交换机和队列，使用的是FANOUT类型的交换机，绑定方式是直接绑定
$channel->queue_bind(QUEUE_NAME1, EXCHANGE_NAME);
$channel->queue_bind(QUEUE_NAME2, EXCHANGE_NAME);
// 6、 发消息到交换机
for ($i = 0; $i < 1; $i++) {
    $msg = new AMQPMessage('Hello World!'.$i);
    $channel->basic_publish($msg, EXCHANGE_NAME, '');
    echo $i." [x] Sent 'Hello World!'\n";
}

$channel->close();
$rabbitConnection->closeConnection();
