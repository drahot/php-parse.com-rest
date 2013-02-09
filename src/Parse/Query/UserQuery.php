<?php

namespace Parse\Query;

use Parse\User;

/**
 * Parse User Query Class
 * 
 * @author drahot
 */
class UserQuery extends Query
{

    /**
     * EndPoint Url
     * @var string
     */
    private $endPointUrl;

    /**
     * Description
     * @return type
     */
    public function __construct()
    {
        $this->endPointUrl = implode('/', array(self::API_PATH, 'users'));
    }

    /**
     * Get EndPoint Url
     * 
     * @return string
     */
    public function getEndPointUrl()
    {
        return $this->endPointUrl;
    }

    /**
     * Create User Instance
     * 
     * @param array $data 
     * @return array
     */
    protected function createInstance(array $data)
    {
        $username = $data["username"];
        unset($data["username"]);
        $password = null;
        if (isset($data["password"])) {
            $password = $data["password"];
            unset($data["password"]);
        }
        return new User($username, $password, $data);
    }

}