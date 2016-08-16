<?php

namespace Imhonet\Connection\DataFormat\Hash\RabbitMQ;

use Imhonet\Connection\DataFormat\IArr;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Wait implements IArr
{
    /**
     * @var AMQPChannel
     */
    protected $channel;
    /**
     * @var array
     */
    protected $cache;
    /**
     * @var AMQPMessage
     */
    protected $message;
    private $queue;

    /**
     * @inheritdoc
     */
    public function setData($channel)
    {
        $this->channel = $channel;
        $this->channel->basic_consume($this->getQueue(), '', false, false, false, false, [$this, 'setMessage']);

        return $this;
    }

    public function setMessage(AMQPMessage $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function formatData()
    {
        if ($this->message) {
            $this->message->delivery_info['channel']->basic_ack($this->message->delivery_info['delivery_tag']);
            unset($this->message);
        }

        $this->channel->wait();

        return json_decode($this->message->getBody());
    }

    /**
     * @inheritdoc
     */
    public function formatValue()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param mixed $queue
     * @return $this
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }
}
