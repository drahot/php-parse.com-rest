<?php

namespace Parse;

/**
 * Parse Resource Class
 * 
 * @author drahot
 */
abstract class Resource extends Parse
{

    /**
     * Data Container
     * 
     * @var array
     */
    protected $data = array();

    /**
     * Constructor
     * 
     * @param array $data 
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get Magic Method
     * 
     * @param string $name 
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Set Magic Method
     * @param string $name 
     * @param mixed $value 
     * @return void
     * @throws \RuntimeException
     */
    public function __set($name, $value)    
    {
        if (!in_array($name, array('objectId', 'createdAt', 'updatedAt'))) {
            $this->data[$name] = $value;
            return;
        }
        throw new \RuntimeException("{$name} method does not exists!");
    }

}