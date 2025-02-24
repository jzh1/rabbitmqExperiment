<?php
require_once 'RabbitConnection.php';
use PhpAmqpLib\Message\AMQPMessage;
// 1 建立连接
$rabbitConnection = new RabbitConnection();
// 构建channel
$channel = $rabbitConnection->getChannel();




// 声明一个具有延迟插件的自定义交换机
$args = new \PhpAmqpLib\Wire\AMQPTable([
    'x-delayed-type' => \PhpAmqpLib\Exchange\AMQPExchangeType::FANOUT // 这里假设我们使用 direct 类型的交换机
]);
$channel->exchange_declare('delayed_exchange', 'x-delayed-message', false, true, false, false, false, $args);

// 声明延迟队列
$channel->queue_declare('delayed_queue', false, true, false, false);

// 绑定队列到交换机
$channel->queue_bind('delayed_queue', 'delayed_exchange', 'delayed_key');

echo "正在等待延迟队列消息, waiting... \n";

$callback = function (AMQPMessage $message) {
    //$headers = $message->get('application_headers');
    //$nativeData = $headers->getNativeData();
    echo $message->body . '-------' . date('Y/m/d H:i:s') . "\n";
    $message->ack();
};

$channel->basic_consume(
    'delayed_queue',
    '',
    false,
    false,
    false,
    false,
    $callback
);

while ($channel->is_consuming()) {
    $channel->wait();
}




$channel->close();
$rabbitConnection->closeConnection();
