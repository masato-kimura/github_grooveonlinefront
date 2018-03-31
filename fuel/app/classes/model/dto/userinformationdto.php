<?php
namespace model\dto;

class UserInformationDto
{
	private static $instance = null;

	private $last_get_user_information;
	private $count;
	private $comment_count = 0;
	private $artist_review_count = 0;
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

	public function set_last_get_user_information($val)
	{
		$this->last_get_user_information = $val;
	}
	public function get_last_get_user_information()
	{
		return $this->last_get_user_information;
	}

	public function set_count($val)
	{
		$this->count = $val;
	}
	public function get_count()
	{
		return $this->count;
	}

	public function set_comment_count($val)
	{
		$this->comment_count = $val;
	}
	public function get_comment_count()
	{
		return $this->comment_count;
	}

	public function set_artist_reviewt_count($val)
	{
		$this->artist_review_count = $val;
	}
	public function get_artist_review_count()
	{
		return $this->artist_review_count;
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