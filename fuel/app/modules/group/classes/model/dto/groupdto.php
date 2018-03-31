<?php
namespace group\model\dto;

class GroupDto
{
	private static $instance = null;

	private $group_id;
	private $name;
	private $category_id;
	private $category_name;
	private $category_english;
	private $link;
	private $profile_fields;
	private $picture_url;
	private $is_leaved;
	private $leave_date;

	private $group=array(); // array
	private $members=array(); // array

	private function __construct() {

    }

	public static function get_instance()
    {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function set_group_id($group_id)
    {
        $this->group_id = $group_id;
    }
    public function get_group_id()
    {
    	return $this->group_id;
    }

	public function set_name($name)
	{
		$this->name = $name;
	}
	public function get_name()
	{
		return $this->name;
	}

	public function set_category_id($category_id)
	{
		$this->category_id = $category_id;
	}
	public function get_category_id()
	{
		return $this->category_id;
	}

	public function set_category_name($category_name)
	{
		$this->category_name = $category_name;
	}
	public function get_category_name()
	{
		return $this->category_name;
	}

	public function set_category_english($category_english)
	{
		$this->category_english = $category_english;
	}
	public function get_category_english()
	{
		return $this->category_english;
	}

	public function set_link($link)
	{
		$this->link = $link;
	}
	public function get_link()
	{
		return $this->link;
	}

	public function set_profile_fields($profile_fields)
	{
		$this->profile_fields = $profile_fields;
	}
	public function get_profile_fields()
	{
		return $this->profile_fields;
	}

	public function set_picture_url($user_id)
	{
		$this->picture_url = $user_id;
	}
	public function get_picture_url()
	{
		return $this->picture_url;
	}

	public function set_is_leaved($is_leaved)
	{
		$this->is_leaved = $is_leaved;
	}
	public function get_is_leaved()
	{
		return $this->is_leaved;
	}

	public function set_leave_date($leave_date)
	{
		$this->leave_date = $leave_date;
	}
	public function get_leave_date()
	{
		return $this->leave_date;
	}

	public function set_group($group)
	{
		$this->group = $group;
	}
	public function get_group()
	{
		return $this->group;
	}

	public function set_members($members)
	{
		$this->members = $members;
	}
	public function get_members()
	{
		return $this->members;
	}
}
