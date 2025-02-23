<?php
require_once 'RabbitConnection.php';
use PhpAmqpLib\Message\AMQPMessage;

const EXCHANGE_NAME = 'exchange-topic';
const QUEUE_NAME1 = 'topic1';
const QUEUE_NAME2 = 'topic2';
const QUEUE_TYPE = 'topic';


// 1、获取链接对象
$rabbitConnection = new RabbitConnection();
// 2、构建channel
$channel = $rabbitConnection->getChannel();
// 3、构建交换机
$channel->exchange_declare(EXCHANGE_NAME, QUEUE_TYPE, false, false, false);
// 4、构建queue
$channel->queue_declare(QUEUE_NAME1, false, false, false, false);
$channel->queue_declare(QUEUE_NAME2, false, false, false, false);
// 5、 绑定交换机和队列，使用的是topic类型的交换机，绑定方式是直接绑定
//  TOPIC类型的交换机在和队列绑定时，需要以aaa.bbb.ccc..方式编写routingkey
// 其中有两个特殊字符：*（相当于占位符），#（相当通配符）
$channel->queue_bind(QUEUE_NAME1, EXCHANGE_NAME,'*.orange.*');
$channel->queue_bind(QUEUE_NAME2, EXCHANGE_NAME,'*.*.rabbit');
$channel->queue_bind(QUEUE_NAME2, EXCHANGE_NAME,'lazy.#');
// 6、 发消息到交换机
for ($i = 0; $i < 1; $i++) {
    $msg = new AMQPMessage('大橙子!'.$i);
    $channel->basic_publish($msg, EXCHANGE_NAME, 'big.orange.rabbit');
    $msg2 = new AMQPMessage('大黑子!'.$i);
    $channel->basic_publish($msg2, EXCHANGE_NAME, 'small.write.rabbit');
    $channel->basic_publish($msg, EXCHANGE_NAME, 'lazy.da.da.da.da');
    echo $i." [x] Sent 'Hello World!'\n";
}

$channel->close();
$rabbitConnection->closeConnection();
