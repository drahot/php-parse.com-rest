<?php

namespace Parse;

/**
 * Parse User Class
 * 
 * @author drahot
 */
class User extends BaseObject
{

    /**
     * SessionToken
     * 
     * @var string
     */
    private $sessionToken;

    /**
     * Constructor
     * 
     * @param string $username 
     * @param string $password 
     * @param array $data 
     * @return void
     */
    public function __construct($username, $password = null, array $data = array())
    {
        $data['username'] = $username;  
        if (!empty($password)) {
            $data['password'] = $password;
        }   
        parent::__construct("", $data);
        $this->endPointUrl = implode('/', array(self::API_PATH, 'users'));
    }

    /**
     * Get User
     * 
     * @param string $objectId 
     * @return User
     */
    public static function get($objectId)
    {
        $endPointUrl = implode('/', array(self::API_PATH, 'users', $objectId));
        $data = static::_get($endPointUrl);
        $username = $data['username'];
        unset($data['username']);
        return new static($username, null, $data);
    }

    /**
     * Signup
     * 
     * @return void
     */ 
    public function signup()
    {
        if (!isset($this->data['username']) || !isset($this->data['password'])) {
            throw new \InvalidArgumentException();
        }
        try {
            $this->create();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }       

    /**
     * Login
     * 
     * @return void
     */
    public function login()
    {
        $data = array(
            'username' => $this->data['username'],
            'password' => $this->data['password'],
        );
        $url = implode('/', array(static::API_PATH, 'login'));
        $data = static::_get($url, $data);
        $this->sessionToken = $data['sessionToken'];
        unset($data['sessionToken']);
        $this->setProperty($data);
    }

    /**
     * Save User
     * 
     * @return void
     */
    public function save()
    {
        $this->checkLoggedin();
        $headers = array(
            "X-Parse-Session-Token:".$this->sessionToken
        );
        $url = $this->endPointUrl.'/'.$this->objectId;
        $data = static::_put($url, $this->data, $headers);
        $this->data['updatedAt'] = $data['updatedAt'];
    }

    /**
     * Reset Password
     * 
     * @param string $email 
     * @return void
     * @throws \InvalidArgumentException
     */
    public static function resetPassword($email)
    {
        if (empty($email)) {
            throw new \InvalidArgumentException();
        }
        $data = array("email" => $email);
        $url = implode('/', array(static::API_PATH, 'requestPasswordReset'));
        static::_post($url, $data);
    }
    
    /**
     * Delete User
     * 
     * @return void
     */ 
    public function delete()
    {
        $this->checkLoggedin();
        $headers = array(
            "X-Parse-Session-Token:". $this->sessionToken
        );
        $url = $this->endPointUrl.'/'.$this->objectId;
        static::_delete($url, array(), $headers);
        $this->data = array();
        $this->isDeleted = true;
    }
    
    /**
     * Link Facebook Acccount
     * 
     * @param array $authData 
     * @return void
     */
    public function linkFacebookAccount(array $authData)
    {
        $this->data['authData'] = array(
            'facebook' => $authData,
        );
        $this->save();
    }

    /**
     * Link Twitter Account
     * 
     * @param array $authData 
     * @return void
     */
    public function linkTwitterAccount(array $authData)
    {
        $this->data['authData'] = array(
            'twitter' => $authData,
        );
        $this->save();
    }   

    /**
     * Link Facebook and Twitter Account
     * 
     * @param array $facebookAuthData 
     * @param array $twitterAuthData 
     * @return void
     */
    public function linkAccounts(array $facebookAuthData, array $twitterAuthData)
    {
        $this->data['authData'] = array(
            'facebook' => $facebookAuthData,
            'twitter' => $twitterAuthData,
        );
        $this->save();
    }

    /**
     * Unlink Facebook Account
     * 
     * @return void
     */
    public function unlinkFacebookAccount()
    {
        $this->data['authData'] = array(
            'facebook' => null,
        );
        $this->save();
    }

    /**
     * Unlink Twitter Account
     * 
     * @return void
     */
    public function unlinkTwitterAccount()
    {
        $this->data['authData'] = array(
            'twitter' => null,
        );
        $this->save();
    }

    /**
     * Unlink Facebook And Twitter Accounts
     * 
     * @return void
     */
    public function unlinkAccounts()
    {
        $this->data['authData'] = array(
            'facebook' => null,
            'twitter' => null,
        );
        $this->save();
    }

    /**
     * Set Magic Method
     * 
     * @param string $name 
     * @param mixed $value 
     * @return void
     * @throws \RuntimeException
     */
    public function __set($name, $value)    
    {
        if (!in_array($name, array('sessionToken'))) {
            parent::__set($name, $value);
            return;
        }
        throw new \RuntimeException("{$name} method does not exists!");
    }

    /**
     * isLoggined
     * 
     * @return boolean
     */
    private function isLoggedin() 
    {
        $objectId = $this->objectId;
        $sessionToken = $this->sessionToken;
        if (isset($objectId) && isset($sessionToken)) {
            return (!empty($objectId) && !empty($sessionToken));
        }
        return false;
    }

    /**
     * check Login
     * 
     * @return void
     * @throws \RuntimeException
     */
    private function checkLoggedin()
    {
        if (!$this->isLoggedin()) {
            throw new \RuntimeException("Not Loggined!");
        }
    }

}
