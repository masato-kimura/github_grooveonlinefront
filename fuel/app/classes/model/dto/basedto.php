<?php
namespace model\dto;

class BaseDto
{
	private static $instance=null;

	private function __construct(){}

	public static function get_instance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
}