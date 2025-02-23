<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * rabbitmq链接类
 */
class RabbitConnection
{

    const RABBIT_HOST = '47.116.26.112';
    const RABBIT_PORT = '5672';
    const RABBIT_USER = 'admin';
    const RABBIT_PWD = 'rabbitmq';
    const RABBIT_VIR = '/';
    private $connectionFunction;

    public function __construct()
    {

    }

    public function connection()
    {
        $connection = new AMQPStreamConnection(
            self::RABBIT_HOST, self::RABBIT_PORT, self::RABBIT_USER, self::RABBIT_PWD,self::RABBIT_VIR
        );

        $this->connectionFunction = $connection;

        return $connection;
        // $channel = $connection->channel();
    }

    public function getChannel()
    {
        if ($this->connectionFunction){
            return $this->connectionFunction->channel();
        }else{
            return $this->connection()->channel();
        }
    }

    public function closeConnection()
    {
        if ($this->connectionFunction){
            return $this->connectionFunction->close();
        }else{
            return $this->connection()->close();
        }
    }




}
