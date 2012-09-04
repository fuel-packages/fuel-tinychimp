<?php

/**
 * TinyChimp is a lightweight API wrapper for interacting
 * with MailChimp for the Fuel PHP framework
 * 
 * @package		Fuel
 * @author		Max Zender <maxzender@gmail.com>
 * @link		http://github.com/maxzender/fuel-tinychimp
 * @license		MIT License
 */

namespace TinyChimp;

class TinyChimp {
	
	/**
	 * @var string The secret API key
	 */
	protected static $api_key = '';
	
	/**
	 * @var bool Whether or not to use a secure connection
	 */
	protected static $secure_connection = null;
	
	/**
	 * @var string The API Url to be used for connection
	 */
	protected static $api_url = '';
	
	/**
	 * @var int Timeout in seconds, set to 0 for infinite
	 */
	protected static $timeout = null;
	
	/**
	 * @var resource The curl handle
	 */
	private static $_connection = null;
	
	/**
	 * Static constructor called by autoloader
	 */	
	public static function _init()
	{
		$config = \Config::load('tinychimp');
		
		if (empty($config['api_key']))
		{
			throw new \FuelException('API key not specified.');
		}
	
		static::$api_key = $config['api_key'];
		static::$secure_connection = (is_bool($config['secure'])) ? $config['secure'] : false;
		static::$timeout = (is_integer($config['timeout'])) ? $config['timeout'] : 300;
		static::$api_url = (empty($config['api_url'])) ? static::get_api_url() : $config['api_url'];
		
		$connection = curl_init();
		curl_setopt_array($connection, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => static::$timeout,
			CURLOPT_POST           => true
		));
		
		if (static::$secure_connection)
		{
			curl_setopt_array($connection, array(
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => true,
				CURLOPT_PORT           => 443
			));
		}
		
		static::$_connection = $connection;
	}
	
	/**
	 * Get a valid API Url based on the API key
	 *
	 * @return string $api_url The valid API Url
	 */
	public static function get_api_url()
	{
		$api_key = explode('-', static::$api_key);
		if (sizeof($api_key) < 2)
		{
			throw new \FuelException('Invalid API key.');
		}
		$server = end($api_key);
		
		$api_url  = (static::$secure_connection) ? 'https://' : 'http://';
		$api_url .= $server.'.api.mailchimp.com/1.3/?method=';
		
		return $api_url;
	}
	
	/**
	 * Make the actual API call via curl
	 *
	 * @param string $method API method that has been called
	 * @param array $arguments Arguments that have been passed to it
	 * @throws Exception
	 * @return object The response object
	 */
	public static function __callStatic($method, $arguments = array())
	{
		if (strpos($method, '_'))
		{
			// underscore method has been used, make it CamelCase
			$method = \Str::lcfirst(\Inflector::camelize($method));
		}

		$default_params = array(
			'apikey' => static::$api_key
		);
		
		$extra_params = (empty($arguments)) ? $arguments : array_shift($arguments);
		$params = array_merge($default_params, $extra_params);
		
		curl_setopt_array(static::$_connection, array(
			CURLOPT_URL        => static::$api_url.$method,
			CURLOPT_POSTFIELDS => http_build_query($params)
		));
		
		$response = json_decode(curl_exec(static::$_connection));
		if (@$response->error)
		{
			throw new \FuelException('Error #'.$response->code.': '.$response->error);
		}

		return $response;
	}
	
}

/* End of file tinychimp.php */