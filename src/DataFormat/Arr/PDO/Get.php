<?php

namespace Imhonet\Connection\DataFormat\Arr\PDO;


use Imhonet\Connection\DataFormat\IArr;

class Get implements IArr
{
    /**
     * @var \PDOStatement
     */
    private $data;

    private $cache;

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function formatData()
    {
        if ($this->data && $this->cache === null) {
            $this->cache = $this->data->fetchAll(\PDO::FETCH_ASSOC);
            $this->data->closeCursor();
        }

        return $this->cache ? : array();
    }

    public function formatValue()
    {
    }

}
