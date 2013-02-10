<?php

namespace Parse;

use Parse\Data\Binary;
use Parse\Data\GeoPoint;

/**
 * Abstract Object Class
 * 
 * @author drahot
 */
abstract class BaseObject extends Resource
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
    protected $endPointUrl;

    /**
     * Is Deleted
     * @var boolean
     */
    protected $isDeleted = false;

    /**
     * Constructor
     * 
     * @param string $className 
     * @param array $data
     * @return void
     */
    public function __construct($className, array $data = array())
    {
        $this->className = $className;
        $this->setProperty($data);
    }

    /**
     * Delete Object
     * 
     * @param string $objectId 
     * @return void
     */
    public function delete()
    {
        $objectId = $this->objectId;
        if (empty($objectId)) {
            throw new \InvalidArgumentException();
        }
        $url = $this->endPointUrl. '/'. $objectId;
        static::_delete($url);
        $this->data = array();
        $this->className = null;
        $this->isDeleted = true;
    }

    /**
     * Return isDeleted
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Save Object
     * 
     * @return void
     */
    public function save()
    {
        $objectId = $this->objectId;
        if (empty($objectId)) {
            $this->create();
        } else {
            $this->update();
        }
    }
    
    /**
     * Refresh Object
     * 
     * @return void
     */ 
    public function refresh()
    {
        $url = $this->endPointUrl.'/'.$this->objectId;
        $data = static::_get($url);
        $this->setProperty($data);
    }

    /**
     * Create Object
     * 
     * @return void
     */
    protected function create()
    {
        $data = static::_post($this->endPointUrl, $this->getAttributes());
        $this->data['objectId'] = $data['objectId'];
        $this->data['createdAt'] = $data['createdAt'];
        $this->data['updatedAt'] = $data['createdAt'];
    }

    /**
     * Update Object
     * 
     * @return void
     */
    protected function update()
    {
        $url = $this->endPointUrl.'/'.$this->objectId;
        $data = static::_put($url, $this->getAttributes()); 
        $this->data['updatedAt'] = $data['updatedAt'];
    }
                
    /**
     * Has Property
     * 
     * @param string $name 
     * @return bool
     */ 
    public function hasProperty($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Remove Property
     * 
     * @param string $name 
     * @return void
     */
    public function removeProperty($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }

    /**
     * Get ClassName
     * 
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Increment 
     * 
     * @param string $name 
     * @param int $amount 
     * @return void
     */
    public function increment($name, $amount = 1)
    {
        $this->doIncrement($name, $amount);
    }

    /**
     * Decrement
     * 
     * @param string $name 
     * @param int $amount 
     * @return void
     */
    public function decrement($name, $amount = 1)
    {
        $this->doIncrement($name, $amount, true);
    }

    /**
     * Execute Increment
     * 
     * @param string $name 
     * @param int $amount 
     * @return void
     */
    private function doIncrement($name, $amount, $decrement = false)
    {
        $objectId = $this->objectId;
        if (empty($objectId)) {
            throw new \RuntimeException();
        }
        $data = array(
            $name => array(
                '__op'      => 'Increment',
                'amount'    => (!$decrement) ? $amount : $amount * -1,
            )
        );
        $url = $this->endPointUrl.'/'.$objectId;
        $data = static::_put($url, $data);
        $this->data[$name] = $data[$name];
        $this->data['updatedAt'] = $data['updatedAt'];
    }

    /**
     * Set Property
     * 
     * @param array $data 
     * @return void
     */
    protected function setProperty(array $data)
    {
        $result = array();
        foreach ($data as $name => $value) {
            list($name, $value) = $this->convertFromParseType($name, $value);
            $result[$name] = $value;
        }
        $this->data = $result;
    }

    /**
     * Conver To Parse Type
     * 
     * @param string $name 
     * @param mixed $value 
     * @return array
     */
    protected function convertToParseType($name, $value)
    {
        if ($value instanceof Object) {
            $value = array(
                '__type'    => 'Pointer',
                'className' => $value->getClassName(),
                'objectId'  => $value->objectId,
            );
        } elseif ($value instanceof \DateTime) {
            $dateString = $value->format('c');
            $dateString = substr($dateString, 0, -6). 'Z';
            $value = array(
                '__type'    => 'Date',
                'iso'       => $dateString,
            );
        } elseif ($value instanceof Binary) {
            $value = array(
                '__type'    => 'Bytes',
                'base64'    => base64_encode($value->getBinary()),
            );
        } elseif ($value instanceof GeoPoint) {
            $value = array(
                '__type'    => 'GeoPoint',
                'longitude' => $value->getLongitude(),
                'latitude'  => $value->getLatitude(),
            );
        } 
        return array($name, $value);
    }

    /**
     * Convert From Parse Type
     * 
     * @param string $name 
     * @param mixed $value 
     * @return array
     */
    protected function convertFromParseType($name, $value)
    {
        if (is_array($value) && isset($value['__type'])) {
            $type = $value['__type'];
            if ($type === 'Pointer') {
                $value = static::get($value['className'], $value['objectId']);
            } elseif ($type === 'Date') {
                $value = $this->getISO8601ToDatetime($value['iso']);
            } elseif ($type === 'Bytes') {
                $value = new Binary(base64_decode($value['base64']));
            } elseif ($type === 'GeoPoint') {
                $value = new GeoPoint(
                    floatval($value['latitude']), 
                    floatval($value['longitude'])
                );
            } else {
                throw new \RuntimeException();
            }
        }
        return array($name, $value);
    }

    /**
     * Set Public Access All(Read, Write)
     * 
     * @param bool $bool 
     * @return void
     */
    public function setPublicAccessAll($bool = true)
    {
        $bool = (bool) $bool;
        $this->data['ACL'] = array(
            '*' => array(
                'read' => $bool,
                'write' => $bool,
            )
        );
    }
    
    /**
     * Set Public Access Read
     * 
     * @param bool $bool 
     * @return void
     */
    public function setPublicReadAccess($bool = true)
    {
        $bool = (bool) $bool;
        $this->data['ACL'] = array(
            '*' => array(
                'read' => $bool,
            )
        );        
    }

    /**
     * Set Public Access Write
     * 
     * @param bool $bool 
     * @return void
     */
    public function setPublicWriteAccess($bool = true)
    {
        $bool = (bool) $bool;
        $this->data['ACL'] = array(
            '*' => array(
                'write' => $bool,
            )
        );        
    }        

    /**
     * Set ObjectId Access All
     * 
     * @param string $objectId 
     * @param bool $bool 
     * @return void
     */
    public function setAccessAllForObjectId($objectId, $bool = true)
    {
        if (empty($objectId)) {
            throw new \InvalidArgumentException("$objectId is empty!");
        }
        $bool = (bool) $bool;
        $this->data['ACL'] = array(
            $objectId => array(
                'read' => $bool,
                'write' => $bool,
            )
        );        
    }        

    /**
     * Set ObjectId Access Read
     * 
     * @param string $objectId 
     * @param bool $bool 
     * @return void
     */
    public function setAccessReadForObjectId($objectId, $bool = true)
    {
        if (empty($objectId)) {
            throw new \InvalidArgumentException("$objectId is empty!");
        }
        $bool = (bool) $bool;
        $this->data['ACL'] = array(
            $objectId => array(
                'read' => $bool,
            ),
        );
    }

    /**
     * Set ObjectId Access Write
     * 
     * @param string $objectId 
     * @param bool $bool 
     * @return void
     */
    public function setAccessWriteForObjectId($objectId, $bool = true)
    {
        if (empty($objectId)) {
            throw new \InvalidArgumentException("$objectId is empty!");
        }
        $bool = (bool) $bool;
        $this->data['ACL'] = array(
            $objectId => array(
                'write' => $bool,
            )
        );
    }

    /**
     * Set Role Access All
     * 
     * @param string $role 
     * @param bool $bool 
     * @return void
     */
    public function setAccessAllForRole($role, $bool = true)
    {
        if (empty($role)) {
            throw new \InvalidArgumentException("$role is empty!");
        }
        $bool = (bool) $bool;
        $this->data['ACL'] = array(
            'role:'.$role => array(
                'read' => $bool,
                'write' => $bool,
            )
        );        
    }        

    /**
     * Set Role Access Read
     * 
     * @param string $role 
     * @param bool $bool 
     * @return void
     */
    public function setAccessReadForRole($role, $bool = true)
    {
        if (empty($role)) {
            throw new \InvalidArgumentException("$role is empty!");
        }
        $bool = (bool) $bool;
        $this->data['ACL'] = array(
            'role:'.$role => array(
                'read' => $bool,
            ),
        );
    }

    /**
     * Set Role Access Write
     * 
     * @param string $role 
     * @param bool $bool 
     * @return void
     */
    public function setAccessWriteForRole($role, $bool = true)
    {
        if (empty($role)) {
            throw new \InvalidArgumentException("$role is empty!");
        }
        $bool = (bool) $bool;
        $this->data['ACL'] = array(
            'role:'.$role => array(
                'write' => $bool,
            )
        );
    }

    /**
     * Get Attributes
     * 
     * @return array
     */ 
    protected function getAttributes()
    {
        $result = array();
        foreach ($this->data as $name => $value) {
            list($name, $value) = $this->convertToParseType($name, $value);
            $result[$name] = $value;
        }
        return $result;
    }

    /**
     * Convert DateTime
     * 
     * @param string $dateString 
     * @return \DateTime
     */
    private function getISO8601ToDatetime($dateString)
    {
        $dateString = substr($dateString, 0, strpos($dateString, '.'));
        return \DateTime::createFromFormat('Y-m-d\TH:i:s', $dateString);
    }

}