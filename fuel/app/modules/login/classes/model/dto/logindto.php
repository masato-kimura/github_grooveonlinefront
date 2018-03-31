<?php
namespace login\model\dto;

use model\dto\BaseDto;
class LoginDto extends BaseDto
{
	private static $instance = null;

	private $user_id;
	private $user_name;
	private $login_hash;
	private $auth_type;
	private $auto_login;
	private $passreissue_expired_min;
	private $hide_send_btn;
	private $is_first_regist;

	private function __construct() {

	}

	public static function get_instance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function set_user_id($user_id)
	{
		$this->user_id = $user_id;
	}
	public function get_user_id()
	{
		return $this->user_id;
	}

	public function set_user_name($user_name)
	{
		$this->user_name = $user_name;
	}
	public function get_user_name()
	{
		return $this->user_name;
	}

	public function set_login_hash($login_hash)
	{
		$this->login_hash = $login_hash;
	}
	public function get_login_hash()
	{
		return $this->login_hash;
	}

	public function set_auth_type($auth_type)
	{
		$this->auth_type = $auth_type;
	}
	public function get_auth_type()
	{
		return $this->auth_type;
	}

	public function set_auto_login($auto_login)
	{
		$this->auto_login = $auto_login;
	}
	public function get_auto_login()
	{
		return $this->auto_login;
	}

	public function set_passreissue_expired_min($val)
	{
		$this->passreissue_expired_min = $val;
	}
	public function get_passreissue_expired_min()
	{
		return $this->passreissue_expired_min;
	}

	public function set_hide_send_btn($val)
	{
		$this->hide_send_btn = $val;
	}
	public function get_hide_send_btn()
	{
		return $this->hide_send_btn;
	}

	public function set_is_first_regist($val)
	{
		$this->is_first_regist = $val;
	}
	public function get_is_first_regist()
	{
		return $this->is_first_regist;
	}



}
