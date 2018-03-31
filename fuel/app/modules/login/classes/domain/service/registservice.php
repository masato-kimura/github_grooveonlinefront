<?php
namespace login\domain\service;

use Fuel\Core\Config;
use Fuel\Core\Input;
use Fuel\Core\Validation;
use login\domain\service;
use login\model\dto\LoginDto;
use user\model\dto\UserDto;
use model\dto\CurlDto;
use util\Api;

/**
 *
 * @author kimura
 * @return array エラーリクエスト配列、エラーが存在しなければ空配列が帰る
 */
class RegistService extends \model\domain\service\Service {

	public static function validation_for_grooveonlineregistconfirm()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge('regist');
		$obj_validation->add_callable('AddValidation');
		$obj_validation->add_callable('login\domain\service\RegistService');

		// 名前
		$v = $obj_validation->add('user_name', 'お名前');
		$v->add_rule('required');
		$v->add_rule('max_length', 20);
		$v->add_rule('valid_reserve_name');

		// 性別
		$v = $obj_validation->add('gender', '性別');
		$v->add_rule('valid_string', array('alpha'));
		$v->add_rule('max_length', 10);

		// 年齢
		$v = $obj_validation->add('old', '年齢');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', 0);
		$v->add_rule('numeric_max', 1000);
		$v->add_rule('available_birthday'); // 独自ルール

		// 生年月日:年
		$v = $obj_validation->add('birthday_year', '生年月日:年');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('min_length',1);
		$v->add_rule('max_length', 4);

		// 生年月日:月
		$v = $obj_validation->add('birthday_month', '生年月日:月');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('min_length',1);
		$v->add_rule('max_length', 2);

		// 生年月日:日
		$obj_validation->add('birthday_day', '生年月日:日');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('min_length',1);
		$v->add_rule('max_length', 2);

		// 自己紹介
		$v = $obj_validation->add('profile_fields', '自己紹介');
		$v->add_rule('min_length', 1);
		$v->add_rule('max_length', 1000);

		$v = $obj_validation->add('facebook_url', 'Facebookページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('facebook_url');

		$v = $obj_validation->add('google_url', 'Googleページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('google_url');

		$v = $obj_validation->add('twitter_url', 'Twitterページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('twitter_url');

		$v = $obj_validation->add('instagram_url', 'instagramページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('instagram_url');

		$v = $obj_validation->add('site_url', 'ブログ、ウェブサイトアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('address_check');

		// メールアドレス
		$v = $obj_validation->add('email', 'メールアドレス');
		$v->add_rule('required');
		$v->add_rule('valid_email');
		$v->add_rule('is_not_exist_email', 'grooveonline'); // 独自ルール

		// パスワード
		$v = $obj_validation->add('password', 'パスワード');
		$v->add_rule('required');
		$v->add_rule('min_length', 4);
		$v->add_rule('max_length', 20);
		$v->add_rule('valid_string', array('alpha', 'numeric', 'dashes'));// dashes : -_

		if ( ! $obj_validation->run())
		{
			foreach ($obj_validation->error() as $e => $error)
			{
				\Log::info($e);
				switch ($error->rule)
				{
					default:
						static::$_arr_error[$e] = $error->get_message();
				}
			}
		}

		return true;
	}


	public static function validation_for_grooveonlineregistexecute()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge('regist');
		$obj_validation->add_callable('AddValidation');
		$obj_validation->add_callable('login\domain\service\RegistService');

		// 名前
		$v = $obj_validation->add('user_name', 'お名前');
		$v->add_rule('required');
		$v->add_rule('max_length', 20);
		$v->add_rule('valid_reserve_name');

		// 性別
		$v = $obj_validation->add('gender', '性別');
		$v->add_rule('valid_string', array('alpha'));
		$v->add_rule('max_length', 10);

		// 年齢
		$v = $obj_validation->add('old', '年齢');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', 0);
		$v->add_rule('numeric_max', 1000);
		$v->add_rule('available_birthday'); // 独自ルール

		// 生年月日:年
		$v = $obj_validation->add('birthday_year', '生年月日:年');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('min_length',1);
		$v->add_rule('max_length', 4);

		// 生年月日:月
		$v = $obj_validation->add('birthday_month', '生年月日:月');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('min_length',1);
		$v->add_rule('max_length', 2);

		// 生年月日:日
		$obj_validation->add('birthday_day', '生年月日:日');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('min_length',1);
		$v->add_rule('max_length', 2);

		// 都道府県
		$obj_validation->add('pref', '都道府県');
		$v->add_rule('max_length', 30);

		// 自己紹介
		$v = $obj_validation->add('profile_fields', '自己紹介');
		$v->add_rule('min_length', 1);
		$v->add_rule('max_length', 1000);

		$v = $obj_validation->add('facebook_url', 'Facebookページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('facebook_url');

		$v = $obj_validation->add('google_url', 'Googleページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('google_url');

		$v = $obj_validation->add('twitter_url', 'Twitterページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('twitter_url');

		$v = $obj_validation->add('instagram_url', 'instagramページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('instagram_url');

		$v = $obj_validation->add('site_url', 'ブログ、ウェブサイトアドレス');
		$v->add_rule('max_length', 255);

		// メールアドレス
		$v = $obj_validation->add('email', 'メールアドレス');
		$v->add_rule('required');
		$v->add_rule('valid_email');
		$v->add_rule('is_not_exist_email', 'grooveonline'); // 独自ルール

		// パスワード
		$v = $obj_validation->add('password', 'パスワード');
		$v->add_rule('required');
		$v->add_rule('min_length', 4);
		$v->add_rule('max_length', 20);
		$v->add_rule('valid_string', array('alpha', 'numeric', 'dashes'));// dashes : -_

		if ( ! $obj_validation->run())
		{
			foreach ($obj_validation->error() as $e => $error)
			{
				switch ($error->rule)
				{
					default:
						throw new \Exception($error->get_message());
						//static::$_arr_error[$e] = $error->get_message();
				}
			}
		}

		return true;
	}


	public static function validation_for_editregistconfirm()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge('regist');
		$obj_validation->add_callable('AddValidation');
		$obj_validation->add_callable('login\domain\service\RegistService');
		$login_dto = LoginDto::get_instance();

		// 名前
		$v = $obj_validation->add('user_name', 'お名前');
		$v->add_rule('required');
		$v->add_rule('max_length', 20);
		$v->add_rule('valid_reserve_name');

		// 性別
		$v = $obj_validation->add('gender', '性別');
		$v->add_rule('valid_string', array('alpha'));
		$v->add_rule('max_length', 10);

		// 年齢
		$v = $obj_validation->add('old', '年齢');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', 0);
		$v->add_rule('numeric_max', 1000);
		$v->add_rule('available_birthday'); // 独自ルール

		// 生年月日:年
		$v = $obj_validation->add('birthday_year', '生年月日:年');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('min_length',1);
		$v->add_rule('max_length', 4);

		// 生年月日:月
		$v = $obj_validation->add('birthday_month', '生年月日:月');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('min_length',1);
		$v->add_rule('max_length', 2);

		// 生年月日:日
		$obj_validation->add('birthday_day', '生年月日:日');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('min_length',1);
		$v->add_rule('max_length', 2);

		// 自己紹介
		$v = $obj_validation->add('profile_fields', '自己紹介');
		$v->add_rule('min_length', 1);
		$v->add_rule('max_length', 1000);

		$v = $obj_validation->add('facebook_url', 'Facebookページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('facebook_url');

		$v = $obj_validation->add('google_url', 'Googleページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('google_url');

		$v = $obj_validation->add('twitter_url', 'Twitterページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('twitter_url');

		$v = $obj_validation->add('instagram_url', 'instagramページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('instagram_url');

		$v = $obj_validation->add('site_url', 'ブログ、ウェブサイトアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('address_check');

		if ($login_dto->get_auth_type() == '' or $login_dto->get_auth_type() === 'grooveonline') // grooveonline
		{
			// パスワード
			$v = $obj_validation->add('password', 'パスワード');
			$v->add_rule('required');
			$v->add_rule('min_length', 4);
			$v->add_rule('max_length', 20);
			$v->add_rule('valid_string', array('alpha', 'numeric', 'dashes'));// dashes : -_

			// メールアドレス
			$v = $obj_validation->add('email', 'メールアドレス');
			$v->add_rule('required');
			$v->add_rule('valid_email');
			$v->add_rule('is_not_exist_email', 'grooveonline', true); // 独自ルール
		}
		else
		{
			// メールアドレス
			$v = $obj_validation->add('email', 'メールアドレス');
			$v->add_rule('valid_email');
			$v->add_rule('is_not_exist_email', $login_dto->get_auth_type(), true); // 独自ルール
		}

		if ( ! $obj_validation->run())
		{
			foreach ($obj_validation->error() as $e => $error)
			{
				if ($e === 'password')
				{
					// 全部*だったらスルーする
					if (preg_match('/^\*+$/', $error->value))
					{
						\Log::info($error->value);
						continue;
					}
				}
				switch ($error->rule)
				{
					default:
						static::$_arr_error[$e] = $error->get_message();
				}
			}
		}

		return true;
	}


	public static function validation_for_editregistexecute()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge('regist');
		$obj_validation->add_callable('AddValidation');
		$obj_validation->add_callable('login\domain\service\RegistService');
		$login_dto = LoginDto::get_instance();

		// 名前
		$v = $obj_validation->add('user_name', 'お名前');
		$v->add_rule('required');
		$v->add_rule('max_length', 20);
		$v->add_rule('valid_reserve_name');

		// 性別
		$v = $obj_validation->add('gender', '性別');
		$v->add_rule('valid_string', array('alpha'));
		$v->add_rule('max_length', 10);

		// 年齢
		$v = $obj_validation->add('old', '年齢');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', 0);
		$v->add_rule('numeric_max', 1000);
		$v->add_rule('available_birthday'); // 独自ルール

		// 生年月日:年
		$v = $obj_validation->add('birthday_year', '生年月日:年');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('min_length',1);
		$v->add_rule('max_length', 4);

		// 生年月日:月
		$v = $obj_validation->add('birthday_month', '生年月日:月');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('min_length',1);
		$v->add_rule('max_length', 2);

		// 生年月日:日
		$obj_validation->add('birthday_day', '生年月日:日');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('min_length',1);
		$v->add_rule('max_length', 2);

		// 自己紹介
		$v = $obj_validation->add('profile_fields', '自己紹介');
		$v->add_rule('min_length', 1);
		$v->add_rule('max_length', 1000);

		$v = $obj_validation->add('facebook_url', 'Facebookページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('facebook_url');

		$v = $obj_validation->add('google_url', 'Googleページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('google_url');

		$v = $obj_validation->add('twitter_url', 'Twitterページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('twitter_url');

		$v = $obj_validation->add('instagram_url', 'instagramページアドレス');
		$v->add_rule('max_length', 255);
		$v->add_rule('instagram_url');

		$v = $obj_validation->add('site_url', 'ブログ、ウェブサイトアドレス');
		$v->add_rule('max_length', 255);

		if ($login_dto->get_auth_type() == '' or $login_dto->get_auth_type() === 'grooveonline') // grooveonline
		{
			// パスワード
			$v = $obj_validation->add('password', 'パスワード');
			$v->add_rule('required');
			$v->add_rule('min_length', 4);
			$v->add_rule('max_length', 20);
			$v->add_rule('valid_string', array('alpha', 'numeric', 'dashes'));// dashes : -_

			// メールアドレス
			$v = $obj_validation->add('email', 'メールアドレス');
			$v->add_rule('required');
			$v->add_rule('valid_email');
			$v->add_rule('is_not_exist_email', 'grooveonline', true); // 独自ルール
		}
		else
		{
			// メールアドレス
			$v = $obj_validation->add('email', 'メールアドレス');
			$v->add_rule('valid_email');
			$v->add_rule('is_not_exist_email', $login_dto->get_auth_type(), true); // 独自ルール
		}

		if ( ! $obj_validation->run())
		{
			foreach ($obj_validation->error() as $e => $error)
			{
				if ($e === 'password')
				{
					if (preg_match('/^\*+$/', $error->value))
					{
						\Log::info($error->value);
						continue;
					}
				}
				switch ($error->rule)
				{
					default:
						throw new \Exception($error->get_message());
				}
			}
		}

		return true;
	}


	public static function set_dto_for_editregistexecute()
	{
		\Log::info('[start]'. __METHOD__);

		$user_dto  = UserDto::get_instance();
		$login_dto = LoginDto::get_instance();

		$user_dto->set_user_name(\Input::post('user_name'));
		$user_dto->set_date(\Input::post('date'));
		$user_dto->set_password(\Input::post('password'));
		$user_dto->set_email(\Input::post('email'));
		$user_dto->set_gender(\Input::post('gender'));
		$user_dto->set_birthday_year(\Input::post('birthday_year'));
		$user_dto->set_birthday_month(\Input::post('birthday_month'));
		$user_dto->set_birthday_day(\Input::post('birthday_day'));
		$user_dto->set_birthday_secret(\Input::post('birthday_secret'));
		$user_dto->set_old(\Input::post('old'));
		$user_dto->set_old_secret(\Input::post('old_secret'));
		$user_dto->set_pref(\Input::post('pref'));
		$user_dto->set_profile_fields(\Input::post('profile_fields'));
		$user_dto->set_facebook_url(null);
		$user_dto->set_google_url(null);
		$user_dto->set_twitter_url(null);
		$user_dto->set_instagram_url(null);
		$user_dto->set_site_url(null);
		$facebook_url  = preg_replace('/^http[s]*:\/\//i', '', \Input::post('facebook_url'));
		$google_url    = preg_replace('/^http[s]*:\/\//i', '', \Input::post('google_url'));
		$twitter_url   = preg_replace('/^http[s]*:\/\//i', '', \Input::post('twitter_url'));
		$instagram_url = preg_replace('/^http[s]*:\/\//i', '', \Input::post('instagram_url'));
		$site_url      = trim(\Input::post('site_url'));
		if ( ! preg_match('/^(http[s]*:\/\/)/i', $site_url, $match))
		{
			if ( ! empty($site_url))
			{
				$site_url = 'http://'. $site_url;
			}
		}

		if ( ! empty($facebook_url))
		{
			$user_dto->set_facebook_url('https://'. $facebook_url);
		}
		if ( ! empty($google_url))
		{
			$user_dto->set_google_url('https://'. $google_url);
		}
		if ( ! empty($twitter_url))
		{
			$user_dto->set_twitter_url('https://'. $twitter_url);
		}
		if ( ! empty($instagram_url))
		{
			$user_dto->set_instagram_url('https://'. $instagram_url);
		}
		if ( ! empty($site_url))
		{
			$user_dto->set_site_url($site_url);
		}

		$user_dto->set_oauth_id(\Input::post('oauth_id'));

		$login_dto->set_user_name(\Input::post('user_name'));
		$login_dto->set_auth_type($user_dto->get_auth_type());

		if (preg_match('/^[\*]+$/', $user_dto->get_password()))
		{
			$user_dto->set_password('');
		}

		return true;
	}


	public static function set_session_for_grooveonlineregistexecute()
	{
		\Log::debug('[start]'. __METHOD__);

		\Session::set_flash('user_regist_target_id', \Input::param('target_id'));  // 招待するユーザID
		\Session::set_flash('user_regist_invite_id', \Input::param('invite_id'));  // 招待したユーザID
		\Session::set_flash('user_regist_group_id',  \Input::param('group_id'));   // グループID
		\Session::set_flash('invited_by',            \Input::param('invited_by')); // 招待種別 group

		return true;
	}






	/**
	 * メールアドレスが未存在であることを確認する
	 * 未存在：true, 存在する：false
	 * (更新時はメールアドレスに変更がなければtrueを返す)
	 * @param string $email
	 * @param string $auth_type
	 * @param string $is_update
	 * @return boolean
	 */
	public static function _validation_is_not_exist_email($email, $auth_type, $is_update=false)
	{
		\Log::debug('[start]'. __METHOD__);

		Validation::active()->set_message('is_not_exist_email', 'このメールアドレスはすでに登録済みです');

		try
		{
			if (empty($email))
			{
				return true;
			}

			$user_dto = UserDto::get_instance();
			$login_dto = LoginDto::get_instance();
			$login_dto->set_auth_type($auth_type);

			# 更新時はメールアドレスが変更になった場合のみチェックを行います。
			if ($is_update)
			{
				// user_idから登録メールアドレスを取得
				LoginService::set_user_info_to_dto_from_api();
				if ($email == $user_dto->get_email())
				{
					return true;
				}
			}

			$arr_send = array(
					'email'     => $email,
					'auth_type' => $auth_type,
					'is_update' => $is_update,
			);

			# CURL送信のための情報をDTOにセット
			$curl_dto = CurlDto::get_instance();
			$curl_dto->set_url(\Config::get('host.api_url'). 'main/user/isnotregistemail.json');
			$curl_dto->set_arr_send($arr_send);

			# CURLにてAPI送信
			$obj_api = new Api();
			$obj_api->send_curl();

			# CURL送信レスポンス
			$obj_response = $obj_api->get_curl_response();

			if ($obj_response->result->is_not_exist == false)
			{
				return false;
			}

			return true;
		}
		catch (\Exception $e)
		{
			throw new \Exception($e);
		}
	}


	public static function _validation_available_birthday($old)
	{
		\Log::debug('[start]'. __METHOD__);

		Validation::active()->set_message('available_birthday', '誕生月日が正しくありません。');

		$birthday_year = \Input::param('birthday_year');
		if (empty($birthday_year))
		{
			$year = \Date::forge()->format('%Y') - $old;
		}
		else
		{
			$year = $birthday_year;
		}

		$month = \Input::param('birthday_month');
		$day   = \Input::param('birthday_day');

		if ( ! (empty($month)) && ! (empty($day)))
		{
			if ( ! checkdate(\Input::param('birthday_month'), \Input::param('birthday_day'), $year))
			{
				return false;
			}
		}

		return true;
	}


	public static function _validation_facebook_url($url)
	{
		\Log::debug('[start]'. __METHOD__);

		if (empty($url))
		{
			return true;
		}

		$url = preg_replace('/^http[s]*:\/\//', '', $url);
		if ( ! preg_match('/^(www\.|m\.|mobile\.)*facebook\.com\/.+/i', $url, $match))
		{
			$message = 'Facebookのアドレスを確認してください';
			Validation::active()->set_message('facebook_url', $message);
			return false;
		}

		return true;
	}

	public static function _validation_twitter_url($url)
	{
		\Log::debug('[start]'. __METHOD__);

		if (empty($url))
		{
			return true;
		}

		$url = preg_replace('/^http[s]*:\/\//', '', $url);
		if ( ! preg_match('/^(www\.|m\.|mobile\.)*twitter\.com\/.+/i', $url, $match))
		{
			$message = 'Twitterのアドレスを確認してください';
			Validation::active()->set_message('twitter_url', $message);
			return false;
		}

		return true;
	}

	public static function _validation_google_url($url)
	{
		\Log::debug('[start]'. __METHOD__);

		if (empty($url))
		{
			return true;
		}

		$url = preg_replace('/^http[s]*:\/\//', '', $url);
		if ( ! preg_match('/^plus\.google\.com\/.+/i', $url, $match))
		{
			$message = 'Google+のアドレスを確認してください';
			Validation::active()->set_message('google_url', $message);
			return false;
		}

		return true;
	}

	public static function _validation_instagram_url($url)
	{
		\Log::debug('[start]'. __METHOD__);

		if (empty($url))
		{
			return true;
		}

		$url = preg_replace('/^http[s]*:\/\//', '', $url);
		if ( ! preg_match('/^instagram\.com\/.+/i', $url, $match))
		{
			$message = 'instagramのアドレスを確認してください';
			Validation::active()->set_message('instagram_url', $message);
			return false;
		}

		return true;
	}

	public static function _validation_address_check($url)
	{
		\Log::debug('[start]'. __METHOD__);

		if (empty($url))
		{
			return true;
		}

		if (\Input::param('oauth_id') == '113634728993179' and \Input::param('auth_type') == 'facebook')
		{
			return true;
		}

		if (preg_match('/(groove-online.com)/i', $url, $match))
		{
			$message = 'このアドレスは登録できません';
			Validation::active()->set_message('address_check', $message);
			return false;
		}

		return true;
	}



}