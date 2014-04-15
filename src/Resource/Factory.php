<?php

namespace Imhonet\Connection\Resource;

use Imhonet\Connection\Resource\PDO;

class Factory implements IConnect
{
    const TYPE_MYSQL = 'mysql';
    const TYPE_MYSQL_PDO = 'mysql_pdo';
    const TYPE_HANDLERSOCKET = 'handlersocket';
    const TYPE_RCMD = 'rcmd';
    const TYPE_MONGO = 'mongo';
    const TYPE_COUCHBASE = 'couchbase';
    const TYPE_MEMCACHE = 'memcache';
    const TYPE_MEMCACHED = 'memcached';
    const TYPE_SPHINX = 'sphinx';

    private static $instance;
    private $resources = array();
    private $params = array();

    /**
     * @return self
     */
    static public function getInstance()
    {
        return self::$instance ? : self::$instance = new static;
    }

    /**
     * @inheritdoc
     * @return self
     */
    public function setHost($host)
    {
        $this->params['host'] = $host;

        return $this;
    }

    /**
     * @inheritdoc
     * @return self
     */
    public function setPort($port)
    {
        $this->params['port'] = $port;

        return $this;
    }

    /**
     * @inheritdoc
     * @return self
     */
    public function setUser($user)
    {
        $this->params['user'] = $user;

        return $this;
    }

    /**
     * @inheritdoc
     * @return self
     */
    public function setPassword($password)
    {
        $this->params['password'] = $password;

        return $this;
    }

    /**
     * @inheritdoc
     * @return self
     */
    public function setDatabase($database)
    {
        $this->params['database'] = $database;

        return $this;
    }

    /**
     * @inheritdoc
     * @return self
     */
    public function setTable($table)
    {
        $this->params['table'] = $table;

        return $this;
    }

    /**
     * @inheritdoc
     * @return self
     */
    public function setIndexName($name)
    {
        $this->params['index_name'] = $name;

        return $this;
    }

    /**
     * @inheritdoc
     * @return self
     */
    public function setIndexFields(array $fields)
    {
        $this->params['index_fields'] = $fields;

        return $this;
    }

    /**
     * @inheritdoc
     * @return self
     */
    public function setIds($ids)
    {
        $this->params['ids'] = $ids;

        return $this;
    }

    /**
     * @param string $type self::TYPE_MYSQL|self::TYPE_HANDLERSOCKET|...
     * @param array $params
     * @return string
     */
    private function getResourceKey($type, $params)
    {
        return md5($type . json_encode($params));
    }

    /**
     * @param string $type self::TYPE_MYSQL|self::TYPE_HANDLERSOCKET|...
     * @throws \InvalidArgumentException
     * @return IResource
     */
    public function getResource($type)
    {
        $key = $this->getResourceKey($type, $this->params);

        if (!isset($this->resources[$key])) {
            $this->resources[$key] = $this->createResource($type);
        }

        $this->resetParams();

        return $this->resources[$key];
    }

    /**
     * @param $type
     * @return IResource
     * @throws \InvalidArgumentException
     */
    protected function createResource($type)
    {
        switch ($type) {
            case self::TYPE_MYSQL_PDO:
                $resource = $this->getMySQLPDO();
                break;
            case self::TYPE_COUCHBASE:
                $resource = $this->getCouchbase();
                break;
            case self::TYPE_MEMCACHE:
                $resource = $this->getMemcache();
                break;
            case self::TYPE_MEMCACHED:
                $resource = $this->getMemcached();
                break;
            case self::TYPE_SPHINX:
                $resource = $this->getSphinx();
                break;
            default:
                throw new \InvalidArgumentException();
        }

        return $resource;
    }

    /**
     * @return Sphinx
     */
    private function getSphinx()
    {
        $resource = new Sphinx();
        $resource->setHost($this->params['host'])
            ->setPort($this->params['port']);

        return $resource;
    }

    /**
     * @return PDO\MySQL
     */
    private function getMySQLPDO()
    {
        $resource = new PDO\MySQL();
        $resource->setHost($this->params['host'])
            ->setPort(isset($this->params['port']) ? $this->params['port'] : null)
            ->setUser($this->params['user'])
            ->setPassword($this->params['password'])
            ->setDatabase($this->params['database']);

        return $resource;
    }

    /**
     * @return Couchbase
     */
    private function getCouchbase()
    {
        $resource = new Couchbase();
        $resource->setHost($this->params['host'])
            ->setPort($this->params['port'])
            ->setUser($this->params['user'])
            ->setPassword($this->params['password'])
            ->setDatabase($this->params['database']);

        return $resource;
    }

    /**
     * @return Memcached
     */
    private function getMemcached()
    {
        $resource = new Memcached();
        $resource->setHost($this->params['host'])
            ->setPort($this->params['port']);

        return $resource;
    }

    /**
     * @return Memcache
     */
    private function getMemcache()
    {
        $resource = new Memcache();
        $resource->setHost($this->params['host'])
            ->setPort($this->params['port']);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->params['host'];
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->params['port'];
    }

    /**
     * @inheritdoc
     */
    public function getUser()
    {
        return $this->params['user'];
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->params['password'];
    }

    /**
     * @inheritdoc
     */
    public function getDatabase()
    {
        return $this->params['database'];
    }

    /**
     * @inheritdoc
     */
    public function getTable()
    {
        return $this->params['table'];
    }

    /**
     * @inheritdoc
     */
    public function getIndexName()
    {
        return $this->params['index_name'];
    }

    /**
     * @inheritdoc
     */
    public function getIndexFields()
    {
        return $this->params['index_fields'];
    }

    /**
     * @inheritdoc
     */
    public function getIds()
    {
        return $this->params['ids'];
    }

    /**
     * @return void
     */
    private function resetParams()
    {
        $this->params = array();
    }

}
