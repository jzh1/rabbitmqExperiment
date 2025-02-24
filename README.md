## 官方
### 官网地址：https://www.rabbitmq.com
### 官方文档：https://www.rabbitmq.com/tutorials
### 插件下载地址：https://www.rabbitmq.com/community-plugins
https://github.com/rabbitmq/rabbitmq-delayed-message-exchange/releases
因为之前使用并没有系统的整理，所以在此整理下；

## 环境
1. Erlang 25
2. rabbitmq 3.12
3. 注意Erlang这个版本需要centos8+
4. 开启延迟交换机插件

## 1 新建立composer.json

{
    "require": {
        "php-amqplib/php-amqplib": "^3.2"
    }
}

## 2 composer install 安装

## 3 通讯方式
不同的交换机类型，会对routingKey有不同的支持

### 3.1、hello world
* [send1Hello.php](send1Hello.php)  生产者
* [receive1Hello.php](receive1Hello.php) 消费者

都是单一的，一个生产者，一个消费者

### 3.2、work queue
* [send2work.php](send2work.php) 生产者
* [receive2work1.php](receive2work1.php)消费者1
* [receive2work2.php](receive2work2.php)消费者2

**一个生产者，多个消费者；
多个消费者需要一起启动，否则就和通讯方式1没有什么区别**
**举例：**
* 生产者发送4个消息，[1，3]会被分配给消费者1，[2，4]会被分配给消费者2;
* rabbitmq以轮询的方式分配给在线监听的若干队列（有例外情况）
* 但是！ 加流量控制basic_qos + ack可以实现消费能力更强的消费更多的数据，否则的话有木桶效应

### 3.3、publish/subscribe
通过构建exchange（fanout类型的）绑定多个队列，实现把一个消息放到不同的队列，队列的消费者是一个（和通讯1一样的消费者不再实现）
* [send3.php](send3.php) 生产者
* [receive1Hello.php](receive1Hello.php) 消费者

### 3.4、routing
1. 生产者：构建Exchange（direct类型的）在绑定Exchange和Queue时，需要指定好routingKey，同时在发送消息时，也指定routingKey，只有routingKey一致时，才会把指定的消息路由到指定的Queue
2. 根据不同的routing key 链接不同的queue，实现根据routing发送到不同的queue
3. **队列的消费者是一个（和通讯1一样的消费者不再实现）**

### 3.5、topic
构建Exchange（topic类型的）
其中有两个特殊字符：*（相当于占位符），#（相当通配符）
绑定和发送的时候会根据routingkey 进行通配符和占位符的匹配，更灵活了；

### 3.6 confirm
[send6.php](send6.php)rebbitmq confirms确认(确认是否到达交换机)
[send6-2.php](send6-2.php) rebbitmq return确认(确认是否到达队列)

## 4 死信队列和延迟交换机
* 1:消息被消费者拒绝，requeue设置为false
* 2.1:发送消息时设置消息的生存时间，如果生存时间到了，还没有被消费。
* 2.2:也可以指定某个队列中所有消息的生存时间，如果生存时间到了，还没有被消费。
* 3:队列已经达到消息的最大长度后，再路由过来的消息直接变为死信
[send7.php](send7.php) 
* 
**死信队列的应用：**
- 基于死信队列在队列消息已满的情况下，消息也不会丢失
- 实现延迟消费的效果。比如：下订单时，有15分钟的付款时间

## 延迟队列
* [send8.php](send8.php) 生产者
* [receive8.php](receive8.php) 消费者
* 出队列时间会根据过期时间自动出队列，而不是顺序
