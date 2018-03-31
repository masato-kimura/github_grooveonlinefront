<?php
namespace model\dto;

class CurlDto
{
	private static $instance = null;

	private $url;
	private $api_key;
	private $arr_send = array();
	private $header;
	private $timeout = null;
	private $connection_timeout = null;
	private $ssl_varifypeer;

	private function __construct() {}

	public static function get_instance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function set_url($val)
	{
		$this->url = $val;
	}
	public function get_url()
	{
		return $this->url;
	}

	public function set_api_key($val)
	{
		$this->api_key = $val;
	}
	public function get_api_key()
	{
		return $this->api_key;
	}

	public function set_arr_send($val)
	{
		$this->arr_send = $val;
	}
	public function get_arr_send()
	{
		return $this->arr_send;
	}

	public function set_header($val)
	{
		$this->header = $val;
	}
	public function get_header()
	{
		return $this->header;
	}

	public function set_connection_timeout($val)
	{
		$this->connection_timeout = (int)$val;
	}
	public function get_connection_timeout()
	{
		return $this->connection_timeout;
	}

	public function set_timeout($val)
	{
		$this->timeout = (int)$val;
	}
	public function get_timeout()
	{
		return $this->timeout;
	}

	public function set_ssl_varifypeer($val)
	{
		$this->ssl_varifypeer = val;
	}
	public function get_ssl_varifypeer()
	{
		return $this->ssl_varifypeer;
	}
}