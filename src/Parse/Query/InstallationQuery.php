<?php

namespace Parse\Query;

use Parse\Installation;

/**
 * Installation Query Class
 * 
 * @author drahot
 */
class InstallationQuery extends Query
{

    /**
     * Extra HTTP Headers
     * @var array
     */
    private $extraHeaders;

    /**
     * EndPointUrl
     * 
     * @var string
     */
    private $endPointUrl;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->extraHeaders = array(
            'X-Parse-Master-Key' => static::getMasterKey()
        );
        $this->endPointUrl = implode('/', array(self::API_PATH, 'installations'));
    }

    /**
     * Create Installation 
     * 
     * @param array $data 
     * @return Parse\Installation
     */
    protected function createInstance(array $data)
    {
        return new Installation($data);
    }

    /**
     * Get Extra HTTP Headers
     * 
     * @return array
     */
    protected function getExtraHeaders()
    {
        return $this->extraHeaders;     
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