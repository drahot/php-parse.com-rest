<?php

namespace Parse\Query;

use Parse\Object;

/**
 * Parse Object Query Class
 * 
 * @author drahot
 */
class ObjectQuery extends Query
{

    /**
     * ClassName
     * 
     * @var string
     */
    private $className;

    /**
     * EndPoint Url
     * 
     * @var string
     */
    private $endPointUrl;   

    /**
     * Constructor
     * 
     * @param string $className 
     * @return void
     */
    public function __construct($className)
    {
        $this->className = $className;
        $this->endPointUrl = implode('/', array(self::API_PATH, 'classes', $className));
    }

    /**
     * Create Object
     * 
     * @param array $data 
     * @return Parse\Object
     */
    protected function createInstance(array $data)
    {
        return new Object($this->className, $data);
    }

    /**
     * Get EndPointUrl
     * 
     * @return string
     */
    protected function getEndPointUrl()
    {
        return $this->endPointUrl;
    }

}