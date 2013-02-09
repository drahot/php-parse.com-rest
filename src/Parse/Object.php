<?php

namespace Parse;

/**
 * Parse Object Class
 * 
 * @author drahot
 */
class Object extends BaseObject
{

	/**
	 * Constructor
	 * 
	 * @param string $className 
	 * @param array $data 
	 * @return void
	 */
	public function __construct($className, array $data = array())
	{
		parent::__construct($className, $data);
		$this->endPointUrl = implode('/', array(self::API_PATH, 'classes', $className));
	}

	/**
	 * Get Object
	 * 
	 * @param string $objectId
	 * @return Parse\Object
	 */
	public static function get($className, $objectId)
	{
		$url = implode('/', array(self::API_PATH, 'classes', $className, $objectId));
		$data = static::_get($url);
		return new static($className, $data);
	}

}
