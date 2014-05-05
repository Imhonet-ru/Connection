<?php

namespace Imhonet\Connection\DataFormat\Arr\PDO;

use Imhonet\Connection\DataFormat\IArr;

class Group implements IArr
{

    private $data;

    private $groups = [];

    public function addGroup($group_field)
    {
        $this->groups[] = $group_field;

        return $this;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function formatData()
    {
        $result = [];

        foreach ($this->data as $row) {
            $cutted = $row;
            $point = & $result;
            foreach ($this->groups as $group) {
                assert(isset($cutted[$group]));

                if (!isset($point[$cutted[$group]])) {
                    $point[$cutted[$group]] = [];
                }
                $point = & $point[$cutted[$group]];
                unset($cutted[$group]);
            }
            $point[] = $cutted;
        }

        return $result;
    }

    /**
     * @return null
     */
    public function formatValue()
    {
        return null;
    }
}

