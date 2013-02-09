<?php

namespace Parse;

/**
 * Parse Push Class
 * 
 * @author drahot
 */
class Push extends Resource
{

	/**
	 * Send
	 * 
	 * @param string $message 
	 * @param array $params 
	 * @param array $where 
	 * @param array $channels 
	 * @return array
	 */
	public static function send($message, array $params, array $where = array(), array $channels = array())
	{
		$data = array(
			'data' => array(
				'alert' => $message
			),
		);
		$data = array_merge($data, $params);
		if ($channels) {
			$data['channels'] = $channels;
		} else {
			$data['channels'] = array("");
		}
		if ($where) {
			$data['where'] = $where;
		}
		$url = implode('/', array(self::API_PATH, 'push'));
		return static::_post($url, $data);
	}

}