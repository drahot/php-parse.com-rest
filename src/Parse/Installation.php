<?php

namespace Parse;

/**
 * Parse Installation Class
 * 
 * @author drahot
 */
class Installation extends Resource
{

    /**
     * Get Installation
     * 
     * @param type $resourceId 
     * @return type
     */
    public static function get($resourceId)
    {
        static $url = null;
        if (is_null($url)) {
            $url = implode('/', array(self::API_PATH, 'Installations', $resourceId));
        }
        $data = static::_get($url);
        return new static($data);
    }

}