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

// 测试越晚发的越早出，根据过期时间
for ($i = 1; $i < 10; $i++) {
    $delay = 10000; // 默认延迟10秒，单位是毫秒
    $delay = 10000-$i*1000;
    $messageBody = '第'.$i.'个消息，Hello Max!延迟时间：'.($delay/1000).'秒 当前时间：'.date('Y/m/d H:i:s');

    $headers = new \PhpAmqpLib\Wire\AMQPTable(['x-delay' => $delay]);
    $message = new AMQPMessage($messageBody, ['delivery_mode' => 2]);
    $message->set('application_headers', $headers);

// 发布消息到交换机
    $channel->basic_publish($message, 'delayed_exchange', 'delayed_key');

    echo "Sent {$messageBody} with delay {$delay}ms\n";
    $datetime = date('Y/m/d H:i:s');
    echo "成功发送延迟消息 : {$messageBody} , {$datetime} \n";
}






$channel->close();
$rabbitConnection->closeConnection();
