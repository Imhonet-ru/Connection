<?php

namespace Imhonet\Connection\DataFormat;

interface IDataFormat
{
    /**
     * @param $data
     * @return $this
     */
    public function setData($data);

    /**
     * @return array|\Traversable
     */
    public function formatData();

    /**
     * @return int|float|string|null
     */
    public function formatValue();
}
