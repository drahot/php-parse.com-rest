<?php

namespace Parse;

/**
 * Parse Base Class
 * 
 * @author drahot
 */
abstract class Parse 
{

    /**
     * API PATH
     * @var string
     */ 
    const API_PATH = 'https://api.parse.com/1';

    /**
     * VERSION
     * 
     * @var string 
     */
    const VERSION = "0.1";

    /**
     * GET METHOD
     * 
     * @var string 
     */
    const HTTP_METHOD_GET = 'GET';

    /**
     * POST METHOD
     * 
     * @var string 
     */
    const HTTP_METHOD_POST = 'POST';

    /**
     * PUT METHOD
     * 
     * @var string 
     */
    const HTTP_METHOD_PUT = 'PUT';

    /**
     * DELETE METHOD
     * 
     * @var string 
     */
    const HTTP_METHOD_DELETE = 'DELETE';

    /**
     * Application Id
     * 
     * @var string
     */
    private static $appId;

    /**
     * REST API Key
     * 
     * @var string
     */
    private static $restApiKey;

    /**
     * 
     */
    private static $masterKey;

    /**
     * Initialize 
     * Set Parse.com API Variables
     * 
     * @param string $appId 
     * @param string $restApiKey 
     * @param string $masterKey 
     * @return void
     */
    public static function initialize($appId, $restApiKey, $masterKey)
    {
        self::$appId = $appId;
        self::$restApiKey = $restApiKey;
        self::$masterKey = $masterKey;
    }   

    /**
     * Get Application Id
     * 
     * @return string
     */
    public static function getAppId()
    {
        return self::$appId;
    }

    /**
     * Get REST API Key
     * 
     * @return string
     */
    public static function getRestApiKey()
    {
        return self::$restApiKey;
    }

    /**
     * Get MASTER KEY
     * 
     * @return string
     */
    public static function getMasterKey()
    {
        return self::$masterKey;
    }

    /**
     * HTTP GET REQUEST
     * 
     * @param string $url 
     * @param array $params 
     * @param array $extraHeaders 
     * @return array
     */
    protected static function _get($url, array $params = array(), array $extraHeaders = array())
    {
        return static::request(self::HTTP_METHOD_GET, $url, $params, $extraHeaders);
    }

    /**
     * HTTP POST REQUEST
     * 
     * @param string $url 
     * @param array $params 
     * @param array $extraHeaders 
     * @return array
     */
    protected static function _post($url, array $params = array(), array $extraHeaders = array())
    {
        return static::request(self::HTTP_METHOD_POST, $url, $params, $extraHeaders);
    }

    /**
     * HTTP PUT REQUEST
     * 
     * @param string $url 
     * @param array $params 
     * @param array $extraHeaders 
     * @return array
     */
    protected static function _put($url, array $params = array(), array $extraHeaders = array())
    {
        return static::request(self::HTTP_METHOD_PUT, $url, $params, $extraHeaders);
    }

    /**
     * HTTP DELETE REQUEST
     * 
     * @param string $url 
     * @param array $params 
     * @param array $extraHeaders 
     * @return array
     */
    protected static function _delete($url, array $params = array(), array $extraHeaders = array())
    {
        return static::request(self::HTTP_METHOD_DELETE, $url, $params, $extraHeaders);
    }

    /**
     * HTTP request
     * 
     * @param string $method 
     * @param string $url 
     * @param array $params
     * @param array $extraHeaders
     * @return array
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     */ 
    private static function request(
        $method, 
        $url, 
        array $params = array(), 
        array $extraHeaders = array())
    {
        if (empty(self::$appId) || empty(self::$restApiKey) || empty(self::$masterKey) ) {
            throw new \BadMethodCallException("Initialize method does not call!");
        }
        if (empty($url)) {
            throw new \InvalidArgumentException("$url is empty!");
        }

        $headers = array(
            "X-Parse-Application-Id: ".static::getAppId(),
            "X-Parse-REST-API-Key: ".static::getRestApiKey(),
            "Content-type: application/json",
        );

        if ($extraHeaders) {
            $headers = array_merge($headers, $extraHeaders);
        }

        $postData = null;
        if (in_array($method, array(self::HTTP_METHOD_GET, self::HTTP_METHOD_DELETE))) {
            if ($params) {
                $url .= (substr($url, -1, 1) !== '?') ? '?' : '&';
                $url .= http_build_query($params);
            }
        } elseif (in_array($method, array(self::HTTP_METHOD_POST, self::HTTP_METHOD_PUT))) {
            $postData = static::getPostData($url, $params);
        } else {
            throw new \InvalidArgumentException("Invalid method!");
        }

        list($respCode, $resp) = static::curlRequest($url, $method, $headers, $postData);
        if (!in_array(intval($respCode), array(200, 201))) {
            $error = json_decode($resp);
            if ($error) {
                throw new \RuntimeException($error->error, isset($error->code) ? $error->code : $respCode);
            } else {
                throw new \RuntimeException($respCode);
            }
        }
        $result = json_decode($resp, true);
        return $result;
    }

    /**
     * Http request by cURL
     * 
     * @param string $url 
     * @param string $method 
     * @param array $headers 
     * @param mixed $postData 
     * @return array
     * @throws \RuntimeException
     */
    private static function curlRequest($url, $method, array $headers, $postData)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, "php-parse.com-rest/" . Parse::VERSION);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        if (!empty($postData)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        }
        $resp = curl_exec($curl);
        $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (curl_errno($curl)) {
            $message = curl_error($curl);
            curl_close($curl);
            throw new \RuntimeException($message);
        }
        curl_close($curl);
        return array($respCode, $resp);
    }

    /**
     * Get PostData
     * 
     * @param string $url 
     * @param array $params 
     * @return string
     * @throws \InvalidArgumentException
     */
    private static function getPostData($url, array $params = array())
    {
        if (strpos($url, 'files') !== false) {
            if (count($params) === 0 || empty($params[0])) {
                throw new \InvalidArgumentException("Empty FileData");
            }
            $data = $params[0];
        } else {
            $data = count($params) ? json_encode($params) : "{}";
        }
        return $data;
    }

}
