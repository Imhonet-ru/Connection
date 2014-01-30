<?php

namespace Imhonet\Connection\DataFormat\Hash\Couchbase;

use Imhonet\Connection\DataFormat\IDataFormat;

class Get implements IDataFormat
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @inheritdoc
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function formatData()
    {
        return sizeof($this->data) ? json_decode(current($this->data), true) : array();
    }

    /**
     * @inheritdoc
     */
    public function formatValue()
    {
    }

}
