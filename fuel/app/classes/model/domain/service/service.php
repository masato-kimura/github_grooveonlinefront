<?php
namespace model\domain\service;

use login\domain\service\SessionService;
class Service
{
	protected static $_obj_request = null;
	protected static $_arr_error  = array();

	/**
	 * JSONリクエストをphp:://inputから取得
	 * stdClassに変換しメンバ変数にセット
	 */
	public static function get_json_request()
	{
		\Log::debug('[start]'. __METHOD__);

		# リクエストを取得
		$handle = fopen('php://input', 'r');
		$json_request = fgets($handle);
		fclose($handle);
		static::$_obj_request = json_decode($json_request);

		return true;
	}


	public static function get_arr_error()
	{
		\Log::debug('[start]'. __METHOD__);

		return static::$_arr_error;
	}


	/**
	 * セッションからユーザ情報を取得する
	 * @return multitype:
	 */
	protected static function _get_user_info_from_session()
	{
		\Log::debug('[start]'. __METHOD__);

		$arr_user_info = \Session::get('user_info');

		if (empty($arr_user_info))
		{
			return array();
		}
		else
		{
			$arr_val = \Session::get('user_info');
			$arr_val['is_first_regist'] = false;
			\Session::set('user_info', $arr_val);

			return $arr_user_info;
		}
	}


	/**
	 * # バリデーションで利用するためobj_requestの値を$_POSTにセットする
	 * @param unknown $obj_request
	 * @return boolean
	 */
	protected static function _set_request_to_post($obj_request)
	{
		\Log::debug('[start]'. __METHOD__);

		foreach ($obj_request as $i => $val)
		{
			$_POST[$i] = $val;
		}

		return true;
	}


	/**
	 * セッションユーザ情報をバリデーションで利用するため$_POSTにセットする
	 */
	protected static function _set_session_to_post()
	{
		\Log::debug('[start]'. __METHOD__);

		$arr_user_info = SessionService::get('user_info');
		if (empty($arr_user_info)){
			return true;
		}

		foreach ($arr_user_info as $i => $val)
		{
			$_POST[$i] = $val;
		}

		return true;
	}

	protected static function _validate_base($validate)
	{
		\Log::debug('[start]'. __METHOD__);

		return true;
	}

	protected static function _validate_run($validate, array $arr_params=array())
	{
		\Log::debug('[start]'. __METHOD__);

		if ( ! $validate->run($arr_params))
		{
			foreach ($validate->error() as $error)
			{
				\Log::error($error->get_message());
			}

			throw new \Exception('validate error');
		}

		return true;
	}


}