## 官方地址：https://www.rabbitmq.com/tutorials/tutorial-two-php
## 1 新建立composer.json

{
    "require": {
        "php-amqplib/php-amqplib": "^3.2"
    }
}

## 2 composer install 安装

## 3 通讯方式
### hello world
[send1Hello.php](send1Hello.php)  生产者
[receive1Hello.php](receive1Hello.php)receive.php 消费者

都是单一的，一个生产者，一个消费者

### work queue
[send2work.php](send2work.php) 
[receive2work1.php](receive2work1.php)
[receive2work2.php](receive2work2.php)

一个生产者，多个消费者；
