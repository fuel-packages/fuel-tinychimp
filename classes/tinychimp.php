<?php

/**
 * TinyChimp is a lightweight API wrapper for interacting
 * with MailChimp for the Fuel PHP framework
 * 
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
	protected $api_key = '';
	
	/**
	 * @var bool Whether or not to use a secure connection
	 */
	protected $secure_connection = null;
	
	/**
	 * @var string The API Url to be used for connection
	 */
	protected $api_url = '';
	
	/**
	 * @var int Timeout in seconds, set to 0 for infinite
	 */
	protected $timeout = null;
	
	/**
	 * @var resource The curl handle
	 */
	private $_connection = null;
	
	/**
	 * Create a new instance of the API wrapper
	 *
	 * @param array $config
	 * @return object TinyChimp
	 */
	public function factory($config = array())
	{
		$default_config = \Config::load('tinychimp');
		
		if (is_array($default_config) and is_array($config))
		{
			$config = array_merge($default_config, $config);
		}
		
		return new self($config);
	}
	
	/**
	 * Constructor method
	 *
	 * @param array $config
	 */
	public function __construct($config)
	{
		if (empty($config['api_key']))
		{
			throw new \Fuel_Exception('API key not specified.');
		}
	
		$this->api_key = $config['api_key'];
		$this->secure_connection = (is_bool($config['secure'])) ? $config['secure'] : false;
		$this->timeout = (is_integer($config['timeout'])) ? $config['timeout'] : 300;
		$this->api_url = (empty($config['api_url'])) ? $this->get_api_url() : $config['api_url'];
		
		$this->_connection = curl_init();
		curl_setopt_array($this->_connection, array(
			CURLOPT_RETURNTRANSFER 	=> true,
			CURLOPT_TIMEOUT			=> $this->timeout,
			CURLOPT_POST 			=> true
		));
		
		if ($this->secure_connection)
		{
			curl_setopt_array($this->_connection, array(
				CURLOPT_SSL_VERIFYPEER	=> false,
				CURLOPT_SSL_VERIFYHOST	=> true,
				CURLOPT_PORT 			=> 443
			));
		}
	}
	
	/**
	 * Get a valid API Url based on the API key
	 *
	 * @return string $api_url The valid API Url
	 */
	public function get_api_url()
	{
		$api_key = explode('-', $this->api_key);
		if (sizeof($api_key) < 2)
		{
			throw new \Fuel_Exception('Invalid API key.');
		}
		$server = end($api_key);
		
		$api_url  = ($this->secure_connection) ? 'https://' : 'http://';
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
	public function __call($method, $arguments = array())
	{
		if (strpos($method, '_'))
		{
				// underscore method has been used, make it CamelCase
			$method = lcfirst(\Inflector::camelize($method));
		}

		$default_params = array(
			'apikey' => $this->api_key
		);
		
		$extra_params = (empty($arguments)) ? $arguments : array_shift($arguments);
			// do a merge here in case a different API key has been passed
		$params = array_merge($default_params, $extra_params);
		
		curl_setopt_array($this->_connection, array(
			CURLOPT_URL 		=> $this->api_url.$method,
			CURLOPT_POSTFIELDS	=> $params
		));
		
		$response = json_decode(curl_exec($this->_connection));
		if (@$response->error)
		{
			throw new \Fuel_Exception('Error #'.$response->code.': '.$response->error);
		}

		return $response;
	}
	
	/**
	 * Destructor method
	 */
	public function __destruct()
	{
		curl_close($this->_connection);
	}
	
}

/* End of file tinychimp.php */