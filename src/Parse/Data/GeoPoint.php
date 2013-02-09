<?php

namespace Parse\Data;

/**
 * Parse GeoPoint
 * 
 * @author drahot
 */
class GeoPoint
{

	/**
	 * latitude
	 * 
	 * @var float
	 */
	private $latitude;

	/**
	 * longitude
	 * 
	 * @var float
	 */
	private $longitude;

	/**
	 * Constructor
	 * 
	 * @param float $latitude 
	 * @param float $longitude 
	 * @return void
	 */
	public function __construct($latitude, $longitude) 
	{
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}

	/**
	 * Get latitude
	 * 
	 * @return float
	 */
	public function getLatitude()
	{
		return $this->latitude;
	}

	/**
	 * Set Latitude
	 * 
	 * @param float $latitude 
	 * @return void
	 */
	public function setLatitude($latitude)
	{
		$this->latitude = $latitude;
	}

	/**
	 * Get Longitude
	 * 
	 * @return float
	 */
	public function getLongitude()
	{
		return $this->longitude;
	}

	/**
	 * Set Longitude
	 * 
	 * @param float $longitude 
	 * @return void
	 */
	public function setLongitude($longitude)
	{
		$this->longitude = $longitude;
	}

}