<?php
namespace model\dto;

class InformationDto
{
	private static $instance = null;

	private $last_information_name;
	private $count;
	private $arr_list = array();

	private function __construct() {}

	public static function get_instance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function set_last_information_name($val)
	{
		$this->last_information_name = $val;
	}
	public function get_last_information_name()
	{
		return $this->last_information_name;
	}

	public function set_count($val)
	{
		$this->count = $val;
	}
	public function get_count()
	{
		return $this->count;
	}

	public function set_arr_list($val)
	{
		$this->arr_list = $val;
	}
	public function get_arr_list()
	{
		return $this->arr_list;
	}
}