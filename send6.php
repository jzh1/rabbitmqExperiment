<?php
require_once 'RabbitConnection.php';
use PhpAmqpLib\Message\AMQPMessage;
// 1 建立连接
$rabbitConnection = new RabbitConnection();

// 2 构建channel
$channel = $rabbitConnection->getChannel();
const QUEUE_NAME = 'confirms';

// 3 构建queue
$channel->queue_declare(QUEUE_NAME, false, false, false, false);

// 4 开启confirm(确保消息到rabbitmq)
$channel->confirm_select();

// 5 设置confirm异步回掉
$channel->set_ack_handler(
    function (AMQPMessage $message){
        // code when message is confirmed

        echo " [confirmed]\n";
    }
);
$channel->set_nack_handler(
    function (AMQPMessage $message){
        // code when message is nack-ed
        echo " [nack-ed]\n";
    }
);


// 6 发送消息(mandatory 设置为true才会触发rabbitmq的return机制)
$msg = new AMQPMessage('Hello World!');
$channel->basic_publish($msg, '', 'confirm',true);

echo " [x6] Sent 'Hello World!'\n";

// return机制：确保rabbitmq把消息路由到队列
$channel->set_return_listener(function (){
    // code when message is nack-ed
    echo " 消息没有路由到指定队列时，才会触发 \n";
});

// 等待确认
$channel->wait_for_pending_acks(); // 阻塞等待所有消息的确认
$channel->wait_for_pending_acks_returns();
$channel->close();
$rabbitConnection->closeConnection();
