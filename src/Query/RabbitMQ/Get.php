<?php

/**
 * Created by PhpStorm.
 * User: Zolotukhin Sergey
 * Date: 14.06.16
 * Time: 11:53
 */
namespace Imhonet\Connection\Query\RabbitMQ;

use Imhonet\Connection\Query\Query;
use Imhonet\Connection\Resource\RabbitMQ;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Get extends Query
{
    /**
     * @var RabbitMQ
     */
    protected $resource;
    /**
     * @var AMQPMessage
     */
    protected $message;

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

    /**
     * @return string  JSON
     * @throws \Exception
     */
    public function execute()
    {
        try {
            $this->getResource()->basic_qos(null, 1, null);

            return $this->getResource();
        } catch (\Exception $error) {
            $this->error = $error;
        }

        return false;
    }

    /**
     * @return AMQPChannel
     * @throws \Exception
     */
    protected function getResource()
    {
        return parent::getResource();
    }

    public function getCountTotal()
    {
    }
    public function getCount()
    {
    }
    public function getLastId()
    {
    }
}
