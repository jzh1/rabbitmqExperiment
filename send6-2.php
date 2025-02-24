<?php
require_once 'RabbitConnection.php';
use PhpAmqpLib\Message\AMQPMessage;
// 1 建立连接
$rabbitConnection = new RabbitConnection();
// 构建channel
$channel = $rabbitConnection->getChannel();

//开启发布确认
$channel->confirm_select();
//成功到达交换机时执行
$channel->set_ack_handler(function(AMQPMessage $msg){
    echo '入队成功逻辑'.PHP_EOL."\n";
});
$channel->set_nack_handler(function(AMQPMessage $msg){
    echo 'nack'.PHP_EOL."\n";
});
//消息到达交换机,但是没有进入合适的队列,消息回退
$channel->set_return_listener(function (
    $reply_code,
    $reply_text,
    $exchange,
    $routing_key,
    AMQPMessage $msg
) use (
    $channel,
    $rabbitConnection
)
{
    echo '消息退回,入队列失败逻辑'.PHP_EOL;
    $channel->close();
    $rabbitConnection->closeConnection();
});

// 声明交换机
$channel->exchange_declare('test_exchange', 'direct', false, true, false,false,false);

// 发送消息（设置 mandatory 为 true）
$message = new AMQPMessage('Hello, RabbitMQ!', ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
$channel->basic_publish($message, 'test_exchange', 'nonexistent_routing_key', true);

// 等待 Return Listener 触发
$channel->wait_for_pending_acks_returns();

$channel->close();
$rabbitConnection->closeConnection();
