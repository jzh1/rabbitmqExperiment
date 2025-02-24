<?php
require_once 'RabbitConnection.php';
use PhpAmqpLib\Message\AMQPMessage;
// 1 建立连接
$rabbitConnection = new RabbitConnection();
// 构建channel
$channel = $rabbitConnection->getChannel();

// 创建死信交换机和队列
$channel->exchange_declare('dlx_exchange', 'direct', false, true, false);
$channel->queue_declare('dlx_queue', false, true, false, false);
$channel->queue_bind('dlx_queue', 'dlx_exchange', 'dlx_key');

// 创建普通队列，设置 TTL 和 DLX
$args = [
    'x-message-ttl' => 10000, // 消息 10 秒后过期
    'x-dead-letter-exchange' => 'dlx_exchange', // 死信交换机
    'x-dead-letter-routing-key' => 'dlx_key', // 死信路由键
];

$channel->queue_declare(
    'normal_queue', false, true, false,false,false, new \PhpAmqpLib\Wire\AMQPTable($args)
);

// 发送消息到普通队列
$message = new AMQPMessage('Order #123', ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
$channel->basic_publish($message, '', 'normal_queue');
echo "Message sent to normal_queue.\n";

// 消费死信队列
$callback = function ($msg) {
    echo "Message expired: " . $msg->body . "\n";
    // 执行取消逻辑
};

/**  消费死信队列
 $channel->basic_consume('dlx_queue', '', false, true, false, false, $callback);

// 等待消息
while ($channel->is_consuming()) {
    $channel->wait();
}*/


$channel->close();
$rabbitConnection->closeConnection();
