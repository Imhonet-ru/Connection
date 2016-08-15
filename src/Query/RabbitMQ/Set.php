<?php
/**
 * Created by PhpStorm.
 * User: Zolotukhin Sergey
 * Date: 15.07.16
 * Time: 14:50
 */

namespace Imhonet\Connection\Query\RabbitMQ;


use Imhonet\Connection\Query\Query;
use Imhonet\Connection\Resource\RabbitMQ;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Set extends Query
{
    protected $message;
    private $queue;

    /**
     * @var RabbitMQ
     */
    protected $resource;

    /**
     * @return int bitmask of Imhonet\Connection\Query\IQuery::STATUS_*
     */
    public function getErrorCode()
    {
        if ($this->error) {
            return self::STATUS_ERROR;
        }

        return self::STATUS_OK;
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return boolean
     */
    public function execute()
    {
        try {
            if ($this->message === null) throw new \Exception('Не указано сообщение');

            $message = new AMQPMessage();
            $message->setBody(json_encode($this->message));
            $this->getResource()->basic_publish($message, '', $this->getQueue());

            return true;
        } catch (\Exception $error) {
            $this->error = $error;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param string $queue
     * @return $this
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * @return AMQPChannel
     * @throws \Exception
     */
    protected function getResource() {
        return parent::getResource();
    }

    public function getCountTotal(){}
    public function getCount(){}
    public function getLastId(){}
}