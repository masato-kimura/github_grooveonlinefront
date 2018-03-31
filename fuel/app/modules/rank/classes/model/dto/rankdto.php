<?php
namespace Rank\model\dto;

class RankDto
{
	private static $instance = null;

	private $about;
	private $offset;
	private $limit;
	private $arr_list = array();
	private $arr_track_weekly = array();
	private $arr_album_weekly = array();

	private function __construct() {}

	public static function get_instance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function set_about($val)
	{
		$this->about = $val;
	}
	public function get_about()
	{
		return $this->about;
	}

	public function set_offset($val)
	{
		$this->offset = $val;
	}
	public function get_offset()
	{
		return $this->offset;
	}

	public function set_limit($val)
	{
		$this->limit = $val;
	}
	public function get_limit()
	{
		return $this->limit;
	}

	public function set_arr_track_weekly($val)
	{
		$this->arr_track_weekly = $val;
	}
	public function get_arr_track_weekly()
	{
		return $this->arr_track_weekly;
	}

	public function set_arr_album_weekly($val)
	{
		$this->arr_album_weekly = $val;
	}
	public function get_arr_album_weekly()
	{
		return $this->arr_album_weekly;
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
