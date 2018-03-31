<?php
namespace Api\domain\service;

use model\domain\service\Service;
use login\model\dto\LoginDto;
use model\dto\CurlDto;
use util\Api;
use login\domain\service\LoginService;
use Fuel\Core\Validation;
use favorite\model\dto\FavoriteUserDto;
use Oil\Exception;

final class ApiFavoriteService extends Service
{
	private static $before_count_favorite;
	private static $update_count_favorite;

	public static function validation_for_set()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable(new self());

		$v = $obj_validation->add('favorite_user_id');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validation->add('client_user_id');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('unauthorized_check');
		$v->add_rule('max_length', '19');

		$v = $obj_validation->add('status');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', 0);
		$v->add_rule('numeric_max', 1);

		$arr_value = array(
			'favorite_user_id' => static::$_obj_request->favorite_user_id,
			'client_user_id'   => static::$_obj_request->client_user_id,
			'status'           => static::$_obj_request->status,
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


	public static function set_dto_for_set()
	{
		\Log::debug('[start]'. __METHOD__);

		$favorite_dto = FavoriteUserDto::get_instance();
		$login_dto    = LoginDto::get_instance();

		LoginService::set_user_info_to_dto_from_session();
		$user_id = $login_dto->get_user_id();

		if (isset($user_id))
		{
			$favorite_dto->set_client_user_id($user_id);
		}

		$favorite_dto->set_favorite_user_id(static::$_obj_request->favorite_user_id);
		$favorite_dto->set_status(static::$_obj_request->status);

		return true;
	}


	public static function set_session()
	{
		\Log::debug('[start]'. __METHOD__);

		$favorite_dto = FavoriteUserDto::get_instance();

		$arr_favorite_user = \Session::get('favorite_users', array());
		static::$before_count_favorite = count($arr_favorite_user);

		switch ($favorite_dto->get_status())
		{
			case '0':
				if (empty($arr_favorite_user)) return true;
				unset($arr_favorite_user[$favorite_dto->get_favorite_user_id()]);
				static::$update_count_favorite = count($arr_favorite_user);
				\Session::set('favorite_users', $arr_favorite_user);
				break;
			case '1':
				$arr_favorite_user[$favorite_dto->get_favorite_user_id()] = $favorite_dto->get_favorite_user_id();
				static::$update_count_favorite = count($arr_favorite_user);
				\Session::set('favorite_users', $arr_favorite_user);
				break;
		}

		return true;
	}


	public static function send_api()
	{
		\Log::debug('[start]'. __METHOD__);

		if (static::$before_count_favorite == static::$update_count_favorite)
		{
			\Log::info('すでに登録済みではないか？');
			\Log::info(\Session::get('favorite_user'));
			//return true;
		}

		$favorite_dto = FavoriteUserDto::get_instance();
		$login_dto    = LoginDto::get_instance();

		$arr_send = array();
		$arr_send['client_user_id']    = $login_dto->get_user_id();
		$arr_send['login_hash']        = $login_dto->get_login_hash();
		$arr_send['favorite_user_id']  = $favorite_dto->get_favorite_user_id();
		$arr_send['status']            = $favorite_dto->get_status();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/favorite/set.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$result = $obj_api->get_curl_response()->result->favorite_user_id;

		if ($result === false)
		{
			switch ($favorite_dto->get_status())
			{
				case '0':
					// セッションに戻す
					$arr_favorite_user = \Session::get('favorite_users', array());
					$arr_favorite_user[$favorite_dto->get_favorite_user_id()] = $favorite_dto->get_favorite_user_id();
					\Session::set('favorite_users', $arr_favorite_user);
					break;
				case '1':
					// セッションから除外
					$arr_favorite_user = \Session::get('favorite_users', array());
					unset($arr_favorite_user[$favorite_dto->get_favorite_user_id()]);
					\Session::set('favorite_users', $arr_favorite_user);
					break;
			}

			throw new Exception('favorite_apiからレスポンスがありません');
		}

		return true;
	}


	public static function _validation_unauthorized_check($client_user_id)
	{
		\Log::debug('[start]'. __METHOD__);

		# セッションからログインユーザ情報を取得
		$arr_user_info = static::_get_user_info_from_session();
		if (empty($arr_user_info['user_id']))
		{
			$message = "ログインがセッションから確認できません";
			Validation::active()->set_message('unauthorized_check', $message);

			return false;
		}

		if ($client_user_id != $arr_user_info['user_id'])
		{
			\Log::info($client_user_id. '!='. $arr_user_info['user_id']);
			$message = "ログインユーザと異なるクライアントユーザでアクセスされました";
			Validation::active()->set_message('unauthorized_check', $message);

			return false;
		}

		return true;
	}

}
