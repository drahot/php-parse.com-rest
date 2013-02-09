<?php

namespace Parse\Query;

use Parse\Parse;

/**
 * Parse Query Class
 * 
 * @author drahot
 */
abstract class Query extends Parse
{

    /**
     * Where Data
     * 
     * @var array
     */
    private $where = array();

    /**
     * Options Data
     * 
     * @var array
     */
    private $options = array();

    /**
     * Or Queries
     * 
     * @var array
     */
    private $ors = array();

    /**
     * Create Instance
     * 
     * @param array $data 
     * @return Resource
     */
    protected abstract function createInstance(array $data);

    /**
     * Equal
     * 
     * @param string $name 
     * @param string $value 
     * @return Query
     */
    public function eq($name, $value)
    {
        $this->where[$name] = $value;
        return $this;
    }

    /**
     * Not Equal
     * 
     * @param string $name 
     * @param string $value 
     * @return Query
     */
    public function ne($name, $value)
    {
        $this->setWhereArray($name);
        $this->where[$name]['$ne'] = $value;
        return $this;
    }

    /**
     * LessThan
     * 
     * @param string $name 
     * @param string $value 
     * @return Query
     */
    public function lt($name, $value)
    {
        $this->setWhereArray($name);
        $this->where[$name]['$lt'] = $value;
        return $this;
    }

    /**
     * LessThanEqual
     * 
     * @param string $name 
     * @param string $value 
     * @return Query
     */
    public function lte($name, $value)
    {
        $this->setWhereArray($name);
        $this->where[$name]['$lte'] = $value;
        return $this;
    }

    /**
     * GreaterThan
     * 
     * @param string $name 
     * @param string $value 
     * @return Query
     */
    public function gt($name, $value)
    {
        $this->setWhereArray($name);
        $this->where[$name]['$gt'] = $value;
        return $this;
    }

    /**
     * GreaterThanEqual
     * 
     * @param string $name 
     * @param string $value 
     * @return Query
     */
    public function gte($name, $value)
    {
        $this->setWhereArray($name);
        $this->where[$name]['$gte'] = $value;
        return $this;
    }

    /**
     * In
     * 
     * @param string $name 
     * @param mixed $value 
     * @return Query
     */
    public function in($name, $value)
    {
        $this->setWhereArray($name);
        $this->where[$name]['$in'] = (array) $value;
        return $this;
    }

    /**
     * Not In
     * @param string $name 
     * @param string $value 
     * @return Query
     */
    public function nin($name, $value)
    {
        $this->setWhereArray($name);
        $this->where[$name]['$nin'] = (array) $value;
        return $this;
    }

    /**
     * RelatedTo
     * @param string $name 
     * @param array $value 
     * @return Query
     */
    public function relatedTo($name, array $value)
    {
        $data = array("object" => $value, "key" => $name);
        $this->where['$relatedTo'] = $data;
        return $this;
    }
    
    /**
     * InQuery
     * TODO: test
     * 
     * @param string $name 
     * @param Query $query 
     * @return Query
     */
    public function inQuery($name, Query $query)
    {
        $this->setWhereArray($name);
        $data = array('where' => $query->where);
        $this->where[$name]['$inQuery'] = json_encode($data);
        return $this;
    }

    /**
     * NotInQuery
     * TODO: test
     * 
     * @param string $name 
     * @param Query $query 
     * @return Query
     */
    public function notInQuery($name, Query $query)
    {
        $this->setWhereArray($name);
        $data = array('where' => $query->where);
        $this->where[$name]['$notInQuery'] = json_encode($data);
        return $this;
    }

    /**
     * Exists
     * 
     * @param string $name 
     * @param string $value 
     * @return Query
     */
    public function exists($name, $value = true)
    {
        $this->setWhereArray($name);
        $this->where[$name]['$exists'] = $value;
        return $this;
    }

    /**
     * Add Or Query
     * @param Query $query 
     * @return Query
     */
    public function addOrQuery(Query $query)
    {
        $this->ors[] = $query->where;
        return $this;
    }

    /**
     * Count
     * @return Query
     */
    public function count()
    {
        $this->options['count'] = 1;
        return $this;
    }

    /**
     * Limit
     * 
     * @param int $limit 
     * @return Query
     * @throws \InvalidArgumentException
     */
    public function limit($limit)
    {
        if ($limit < 0 || $limit > 1000) {
            throw new \InvalidArgumentException();
        }
        $this->options['limit'] = $limit;
        return $this;
    }

    /**
     * Skip 
     * 
     * @param int $skip 
     * @return Query
     */
    public function skip($skip)
    {
        $this->options['skip'] = $skip;
        return $this;       
    }

    /**
     * Order
     * 
     * @param string $name 
     * @param bool $descending 
     * @return Query
     */
    public function order($name, $descending = false)
    {
        $order = '';
        if (isset($this->options['order'])) {
            $order = $this->options['order']; 
        }
        if (strlen($order) > 0) {
            $order .= ',';
        }
        $order .= (!$descending) ? $name : '-'.$name;
        $this->options['order'] = $order;
        return $this;
    }

    /**
     * Add Include
     * 
     * @param string $name 
     * @return Query
     */
    public function addInclude($name)
    {
        $this->options['include'] = $name;
        return $this;
    }

    /**
     * Execute Query
     * 
     * @return array
     */
    public function execute()
    {
        $params = $this->makeWhereParams();
        $extraHeaders = $this->getExtraHeaders();
        $response = static::_get($this->getEndPointUrl(), $params, $extraHeaders);
        $list = array();
        foreach ($response["results"] as $data) {
            $list[] = $this->createInstance($data);
        }
        if (isset($response["count"])) {
            return array(
                "results" => $list, 
                "count" => $response["count"]
            );
        }
        return $list;
    }

    /**
     * Reset Query
     * 
     * @return void
     */
    public function reset()
    {
        $this->where = array();
        $this->options = array();
        $this->ors = array();
    }

    /**
     * Get EndPointUrl
     * 
     * @return string
     */
    protected abstract function getEndPointUrl();

    /**
     * Get Extra Headers
     * 
     * @return array
     */
    protected function getExtraHeaders()
    {
        return array();     
    }

    /**
     * Set Where Array
     * 
     * @param string $name 
     * @return void
     */
    private function setWhereArray($name)
    {
        if (!isset($this->where[$name])) {
            $this->where[$name] = array();
        }
    }

    /**
     * Make Where Params
     * 
     * @return array
     */
    private function makeWhereParams()
    {
        $options = $this->options;
        $where = array();

        if (!$this->ors) {
            if ($this->where) {
                $where = $this->where;
            }
        } else {
            if ($this->where || count($this->ors) > 1) {
                $where['$or'] = array();
            }
            if (count($this->ors) > 1) {
                foreach ($this->ors as $or) {
                    $where['$or'][] = $or;                  
                }
                if ($this->where) {
                    $where['$or'][] = $this->where;
                }
            } else {                
                if (isset($where['$or'])) {
                    $where['$or'][] = $this->ors[0];
                    $where['$or'][] = $this->where;                 
                } else {
                    $where = $this->ors[0];
                }
            }
        }

        if ($where) {
            $where = json_encode($where);
            $options['where'] = $where;
        }
        return $options;        
    }

}
