<?php
/**
 * Created by PhpStorm.
 * User: Zolotukhin Sergey
 * Date: 14.06.16
 * Time: 12:00
 */

namespace Imhonet\Connection\Resource;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQ implements IResource
{
    private $host;
    private $port;
    private $user;
    private $password;
    /**
     * @var AMQPChannel
     */
    private $channel;
    /**
     * @var AMQPStreamConnection
     */
    private $connect;

    /**
     * @param string $host
     * @return self
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param string|int $port
     * @return self
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @param string $user
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param string $password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string|int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return AMQPChannel
     */
    public function getHandle()
    {
        if (!$this->channel) {
            $this->channel = $this->getConnect()->channel();
        }

        return $this->channel;
    }

    /**
     * @param string $queue
     * @return $this
     */
    public function queueDeclare($queue)
    {
        $this->getHandle()->queue_declare($queue, false, false, false, false);

        return $this;
    }

    /**
     * @return AMQPStreamConnection
     */
    protected function getConnect()
    {
        if (!$this->connect) {
            $this->connect = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password);
        }

        return $this->connect;
    }

    public function disconnect()
    {
    }
    public function setDatabase($database)
    {
    }
    public function getDatabase()
    {
    }
}
