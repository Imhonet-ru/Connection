<?php

namespace Imhonet\Connection\Query\Sphinx;


use Imhonet\Connection\Query\Query;

class Get extends Query
{
    const FILTER_VALUES = 'values';
    const FILTER_EXCLUDE = 'exclude';

    private $index;
    private $search;
    private $search_fields;
    private $limit;
    private $offset = 0;
    private $sort_field;
    private $sort_order;
    private $field_weights;
    private $filter = [];
    private $match_mode;

    /**
     * @var array|bool
     */
    private $response;
    /**
     * @var bool|null
     */
    private $success;

    /**
     * @param string $name
     * @return self
     */
    public function addIndex($name)
    {
        $this->index = $name;

        return $this;
    }

    /**
     * @param string $str
     * @param array $fields
     * @return self
     */
    public function setSearchString($str, array $fields = [])
    {
        $this->search = $str;
        $this->search_fields = $fields;

        return $this;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return self
     */
    public function setLimit($limit, $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param string $field
     * @param int $order
     * @return self
     * @see \SphinxClient::SetSortMode()
     */
    public function setOrder($field, $order = \SORT_ASC)
    {
        $this->sort_field = $field;
        $this->sort_order = $order == \SORT_ASC ? \SPH_SORT_ATTR_ASC : \SPH_SORT_ATTR_DESC;

        return $this;
    }

    /**
     * @param string $sort
     * @return self
     */
    public function setOrderExpr($sort)
    {
        $this->sort_field = $sort;
        $this->sort_order = \SPH_SORT_EXPR;

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function setFieldWeight($field, $value)
    {
        $this->field_weights[$field] = $value;

        return $this;
    }

    /**
     * @param $attribute
     * @param array $values
     * @param bool $exclude
     * @return self
     */
    public function addFilter($attribute, array $values, $exclude = false)
    {
        $this->filter[$attribute] = [
            self::FILTER_VALUES => $values,
            self::FILTER_EXCLUDE => $exclude
        ];

        return $this;
    }

    /**
     * @param $match_mode
     * @return self
     * @see \SphinxClient::SetMatchMode()
     */
    public function setMatchMode($match_mode)
    {
        $this->match_mode = $match_mode;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        return $this->getResponse();
    }

    /**
     * @return array|bool
     */
    protected function getResponse()
    {
        if (!$this->hasResponse()) {
            try {
                $sph = $this->getResource();
            } catch (\Exception $e) {
                $this->success = false;
                $this->response = false;
            }

            if (isset($sph)) {
                if ($this->sort_field) {
                    $sph->SetSortMode($this->sort_order, $this->sort_field);
                }

                if ($this->limit) {
                    $sph->SetLimits($this->offset, $this->limit);
                }

                if ($this->field_weights) {
                    $sph->SetFieldWeights($this->field_weights);
                }

                foreach ($this->filter as $attr => $data) {
                    $sph->SetFilter($attr, $data[self::FILTER_VALUES], $data[self::FILTER_EXCLUDE]);
                }

                if (!is_null($this->match_mode)) {
                    $sph->SetMatchMode($this->match_mode);
                }

                $sph->addQuery($this->getQuery(), $this->index);

                $this->response = $sph->runQueries();
                $this->success = $this->response !== false;
            }
        }

        return $this->response;
    }

    /**
     * @return string
     */
    protected function getQuery()
    {
        $sph = $this->getResource();
        $this->search = $sph->EscapeString($this->search);
        $query = $this->search;

        if (!empty($this->search)) {
            if (!empty($this->search_fields)) {
                $fields = '@(' . implode(',', $this->search_fields) . ')';
            } else {
                $fields = '@*';
            }
            $query = $fields . ' ' . $this->search;
        }

        return $query;
    }

    /**
     * @return bool
     */
    private function hasResponse()
    {
        return $this->success !== null;
    }

    /**
     * @inheritdoc
     * @return \SphinxClient
     */
    protected function getResource()
    {
        return parent::getResource();
    }

    /**
     * @return bool
     */
    private function isError()
    {
        return $this->getResponse() === false || $this->getResponse()[0]['status'] != 0;
    }

    /**
     * @inheritdoc
     */
    public function getErrorCode()
    {
        return (int) $this->isError();
    }

    /**
     * @inheritdoc
     */
    public function getCountTotal()
    {
        return $this->isError() ? null : $this->getResponse()[0]['total'];
    }

    /**
     * @inheritdoc
     */
    public function getCount()
    {
        return $this->isError() ? null : (!isset($this->getResponse()[0]['matches']) ? 0 : sizeof($this->getResponse()[0]['matches']));
    }

    /**
     * @inheritdoc
     */
    public function getLastId()
    {
    }
}
