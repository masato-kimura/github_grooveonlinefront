<?php
namespace login\domain\service;

class SessionService
{
	public static function set($name, $value, $use_flash_flag=false)
	{
		if ($use_flash_flag == false)
		{
			$login_dto = \login\model\dto\LoginDto::get_instance();
			if ($login_dto->get_auto_login() == true)
			{
				\Log::info('ブラウザ終了でもログイン情報を保持します');
				\Session::instance()->set_config('expiration_time', 60*60*24*90);
				\Session::instance()->set_config('expire_on_close', false);
			}
			else
			{
				\Log::info('ブラウザ終了でログアウトします');
				//\Session::instance()->set_config('expiration_time', 60*60*24*90);
				\Session::instance()->set_config('expire_on_close', true);
			}

			\Log::info('expire_on_close');
			\Log::info(\Session::instance()->get_config('expire_on_close'));

			return \Session::set($name, $value);
		}
		else
		{
			return \Session::set_flash($name, $value);
		}
	}


	public static function get($name, $use_flash_flag=false)
	{
		if ($use_flash_flag == false)
		{
			return \Session::get($name);
		}
		else
		{
			return \Session::get_flash($name);
		}
	}

	public static function delete($name, $use_flash_flag=false)
	{
		if ($use_flash_flag == false)
		{
			return \Session::delete($name);
		}
		else
		{
			return \Session::delete_flash($name);
		}
	}

	public static function destroy()
	{
		return \Session::destroy();
	}

}