## 官方
### 官网地址：https://www.rabbitmq.com
### 官方文档：https://www.rabbitmq.com/tutorials

## 环境
1. Erlang 25
2. rabbitmq 3.12
3. 注意Erlang这个版本需要centos8+

## 1 新建立composer.json

{
    "require": {
        "php-amqplib/php-amqplib": "^3.2"
    }
}

## 2 composer install 安装

## 3 通讯方式
### 1、hello world
* [send1Hello.php](send1Hello.php)  生产者
* [receive1Hello.php](receive1Hello.php) 消费者

都是单一的，一个生产者，一个消费者

### 2、work queue
* [send2work.php](send2work.php) 生产者
* [receive2work1.php](receive2work1.php)消费者1
* [receive2work2.php](receive2work2.php)消费者2

**一个生产者，多个消费者；
多个消费者需要一起启动，否则就和通讯方式1没有什么区别**
**举例：**
* 生产者发送4个消息，[1，3]会被分配给消费者1，[2，4]会被分配给消费者2;
* rabbitmq以轮询的方式分配给在线监听的若干队列（有例外情况）
* 但是！ 加流量控制basic_qos + ack可以实现消费能力更强的消费更多的数据，否则的话有木桶效应

### 3、publish/subscribe
通过构建exchange绑定多个队列（fanout类型的），实现把一个消息放到不同的队列，队列的消费者是一个（和通讯1一样的消费者不再实现）
* [send3.php](send3.php) 生产者
* [receive1Hello.php](receive1Hello.php) 消费者

### 3、routing
根据不同的routing key 链接不同的queue，实现根据routing发送到不同的queue
**队列的消费者是一个（和通讯1一样的消费者不再实现）**
