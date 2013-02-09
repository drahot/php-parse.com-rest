<?php

namespace Parse\Data;

/**
 * Parse Data Class
 * 
 * @author drahot
 */
class Binary
{

	/**
	 * Binary Data
	 * 
	 * @var string
	 */
	private $binary;

	/**
	 * Constructor
	 * 
	 * @param string $binary 
	 * @return void
	 */
	public function __construct($binary)
	{
		$this->binary = $binary;
	}

	/**
	 * Get Binary
	 * 
	 * @return string
	 */
	public function getBinary()
	{
		return $this->binary;
	}
	
	/**
	 * Set Binary
	 * 
	 * @param string $binary 
	 * @return void
	 */
	public function setBinary($binary)
	{
		$this->binary = $binary;
	}

}