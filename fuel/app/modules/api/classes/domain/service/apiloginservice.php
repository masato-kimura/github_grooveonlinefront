<?php
namespace Api\domain\service;

use model\domain\service\Service;
use login\model\dto\LoginDto;
use user\model\dto\UserDto;
use model\dto\CurlDto;
use util\Api;
use login\domain\service\LoginService;
use Fuel\Core\Validation;

final class ApiLoginService extends Service
{
	public static function validation_for_is_available_login()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();

		$v = $obj_validation->add('email');
		$v->add_rule('required');
		$v->add_rule('valid_email');

		$v = $obj_validation->add('password');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric', 'alpha', 'dashes'));

		$arr_value = array(
			'email'    => static::$_obj_request->email,
			'password' => static::$_obj_request->password,
		);

		if ( ! $obj_validation->run($arr_value))
		{
			foreach ($obj_validation->error() as $e => $error)
			{
				throw new \Exception($error->get_message());
			}
		}

		return true;
	}


	public static function set_dto_for_is_available_login()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto = LoginDto::get_instance();
		$user_dto  = UserDto::get_instance();

		// セッションからログイン情報をlogin_dtoにセット
		LoginService::set_user_info_to_dto_from_session();

		$user_dto->set_email(static::$_obj_request->email);
		$user_dto->set_password(static::$_obj_request->password);

		return true;
	}


	public static function is_available_login()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto = LoginDto::get_instance();
		$user_dto  = UserDto::get_instance();

		$arr_send = array();
		$arr_send['user_id']    = $login_dto->get_user_id();
		$arr_send['login_hash'] = $login_dto->get_login_hash();
		$arr_send['email']      = $user_dto->get_email();
		$arr_send['password']   = $user_dto->get_password();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/user/grooveonlineavailablelogin.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$result = $obj_api->get_curl_response()->result->is_available;

		if ($result === false)
		{
			return false;
		}

		return true;
	}
}
