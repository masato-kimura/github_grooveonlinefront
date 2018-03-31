<?php
namespace login\domain\service;

use Fuel\Core\Session;
use Fuel\Core\Validation;
use util;
use user\model\dto\UserDto;
use login\model\dto\LoginDto;
use group\model\dto\GroupDto;
use model\dto\CurlDto;
use util\Api;
use model\domain\service\Service;
use favorite\model\dto\FavoriteUserDto;

final class LoginService extends Service
{
	public static function validation_for_index()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログインしていないこと
		$v = $obj_validation->add('user_id', 'user_id');
		$v->add_rule('not_login'); // 独自ルール

		$login_dto = LoginDto::get_instance();
		$arr_valid_values = array(
				'user_id' => $login_dto->get_user_id(),
		);

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'not_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。すでにログインしています', 7012); // ログイン済
					default:
						throw new \Exception('不明なエラーです');
				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_logout()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログインしていること
		$v = $obj_validation->add('user_id', 'user_id');
		$v->add_rule('is_login'); // 独自ルール

		$login_dto = LoginDto::get_instance();
		$arr_valid_values = array(
				'user_id' => $login_dto->get_user_id(),
		);

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'is_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。ログインしていません', 7012); // 未ログイン
					default:
						throw new \Exception('不明なエラーです');
				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_grooveonlineregistindex()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログインしていないこと
		$v = $obj_validation->add('user_id', 'user_id');
		$v->add_rule('not_login'); // 独自ルール

		// 招待者ID
		$v = $obj_validation->add('invited_by', '招待者ID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', 11);

		// グループID
		$v = $obj_validation->add('group_id', 'グループID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', 11);

		// ターゲットID
		$v = $obj_validation->add('target_id', 'ターゲットID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', 11);

		// 招待ID
		$v = $obj_validation->add('invited_id', '招待ID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', 11);

		$login_dto = LoginDto::get_instance();
		$arr_valid_values = array(
				'user_id'    => $login_dto->get_user_id(),
				'invited_by' => \Input::param('invited_by'),
				'group_id'   => \Input::param('group_id'),
				'target_id'  => \Input::param('target_id'),
				'invite_id'  => \Input::param('invite_id'),
		);

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'not_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。すでにログインしています', 7012); // ログイン済
					default:
						throw new \Exception('不正なリクエストを受診しました');

				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_grooveonlineregistconfirm()
	{
		\Log::debug('[start]' .__METHOD__);

		$obj_validation = Validation::forge('login');
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログインしていないこと
		$v = $obj_validation->add('user_id', 'user_id');
		$v->add_rule('not_login'); // 独自ルール

		$login_dto = LoginDto::get_instance();
		$arr_valid_values = array(
				'user_id' => $login_dto->get_user_id(),
				'email'   => \Input::param('email'),
		);

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'not_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。すでにログインしています', 7012); // ログイン済
					default:
						static::$_arr_error[$i] = $error->get_message();
						return true;
				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_grooveonlineregistexecute()
	{
		\Log::debug('[start]' .__METHOD__);

		$obj_validation = Validation::forge('login');
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログインしていないこと
		$v = $obj_validation->add('user_id', 'user_id');
		$v->add_rule('not_login'); // 独自ルール

		// 招待するユーザID
		$v = $obj_validation->add('target_id');
		$v->add_rule('valid_string', array('numeric'));

		// 招待したユーザID
		$v = $obj_validation->add('invite_id');
		$v->add_rule('valid_string', array('numeric'));

		// グループID
		$v = $obj_validation->add('group_id');
		$v->add_rule('valid_string', array('numeric'));

		// 招待種別 group
		$v = $obj_validation->add('invited_by');
		$v->add_rule('valid_string', array('numeric'));


		$login_dto = LoginDto::get_instance();
		$arr_valid_values = array(
				'user_id'    => $login_dto->get_user_id(),
				'email'      => \Input::param('email'),
				'target_id'  => \Input::param('target_id'),
				'invite_id'  => \Input::param('invite_id'),
				'group_id'   => \Input::param('group_id'),
				'invited_by' => \Input::param('invited_by'),
		);

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'not_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。すでにログインしています', 7012); // ログイン済
					default:
						throw new \Exception($error->get_message());
				} // endswitch
			}// endforeach
		}

		return true;
	}



	public static function validation_for_grooveonlinepassreissuesendmail()
	{
		\Log::debug('[start]' .__METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログインしていないこと
		$v = $obj_validation->add('user_id', 'user_id');
		$v->add_rule('not_login'); // 独自ルール

		// メールアドレス
		$v = $obj_validation->add('email', 'メールアドレス');
		$v->add_rule('required');
		$v->add_rule('valid_email');
		$v->add_rule('is_exist_email'); // 独自ルール
		$v->add_rule('not_available_password_reissued_email'); // 独自ルール

		$login_dto = LoginDto::get_instance();
		$arr_valid_values = array(
			'user_id' => $login_dto->get_user_id(),
			'email'   => \Input::param('email'),
		);

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'not_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。すでにログインしています', 7012); // ログイン済
					case 'not_available_password_reissued_email':
						static::$_arr_error[$i] = "ご指定のアドレスにパスワード再発行メールをすでに送信しております。".
							"再度発行したい場合は". $login_dto->get_passreissue_expired_min(). "分ほどお待ちいただきもう一度お手続きください。";
						$login_dto->set_hide_send_btn(true);
						return true;
					case 'is_exist_email':
						static::$_arr_error[$i] = 'ご指定のメールアドレスは登録されておりません';
						return true;
					default:
						static::$_arr_error[$i] = $error->get_message();
						return true;
				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_grooveonlinepassreissueupdate()
	{
		\Log::debug('[start]' .__METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログインしていないこと
		$v = $obj_validation->add('user_id', 'user_id');
		$v->add_rule('not_login'); // 独自ルール

		// メールアドレス
		$v = $obj_validation->add('email', 'メールアドレス');
		$v->add_rule('required');
		$v->add_rule('valid_email');
		$v->add_rule('is_exist_email'); // 独自ルール

		// 入力パスワード
		$v = $obj_validation->add('password', 'パスワード');
		$v->add_rule('required');
		$v->add_rule('min_length', 4);
		$v->add_rule('max_length', 20);
		$v->add_rule('valid_string', array('alpha', 'numeric', 'dashes'));// dashes : -_

		// 仮発行パスワード
		$v = $obj_validation->add('tentative_password', '仮発行パスワード');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('alpha', 'numeric'));

		// 仮発行ID
		$v = $obj_validation->add('tentative_id');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));

		$login_dto = LoginDto::get_instance();
		$arr_valid_values = array(
				'user_id'            => $login_dto->get_user_id(),
				'email'              => \Input::param('email'),
				'password'           => \Input::param('password'),
				'tentative_password' => \Input::param('tentative_password'),
				'tentative_id'       => \Input::param('tentative_id'),
		);

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'not_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。すでにログインしています', 7012); // ログイン済
					case 'is_exist_email':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移');
					default:
						if ($i === 'password')
						{
							static::$_arr_error[$i] = $error->get_message();
							return true;
						}
						else
						{
							throw new \Exception($error->get_message());
						}
				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_grooveonline()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログインしていないこと
		$v = $obj_validation->add('user_id', 'user_id');
		$v->add_rule('not_login'); // 独自ルール

		// メールアドレス
		$v = $obj_validation->add('email', 'メールアドレス');
		$v->add_rule('required');
		$v->add_rule('valid_email');
		$v->add_rule('is_exist_email'); // 独自ルール

		// 入力パスワード
		$v = $obj_validation->add('password', 'パスワード');
		$v->add_rule('required');
		$v->add_rule('min_length', 4);
		$v->add_rule('max_length', 20);
		$v->add_rule('valid_string', array('alpha', 'numeric', 'dashes'));// dashes : -_

		$login_dto = LoginDto::get_instance();
		$arr_valid_values = array(
				'user_id'            => $login_dto->get_user_id(),
				'email'              => \Input::param('email'),
				'password'           => \Input::param('password'),
		);

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'not_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。すでにログインしています', 7012); // ログイン済
					case 'is_exist_email':
						static::$_arr_error[$i] = $error->get_message();
					default:
						if ($i === 'email' or $i === 'password')
						{
							static::$_arr_error[$i] = $error->get_message();
							return true;
						}
						else
						{
							throw new \Exception($error->get_message());
						}
				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_editregistindex()
	{
		\Log::debug('[start]' .__METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログインしていないこと
		$v = $obj_validation->add('user_id', 'user_id');
		$v->add_rule('is_login'); // 独自ルール

		$login_dto = LoginDto::get_instance();
		$arr_valid_values = array(
				'user_id' => $login_dto->get_user_id(),
		);

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'is_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。すでにログインしています', 7012); // ログイン済
					default:
						if ($i === 'email' or $i === 'password')
						{
							static::$_arr_error[$i] = $error->get_message();
							return true;
						}
						else
						{
							throw new \Exception($error->get_message());
						}
				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_editregistconfirm()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログインしていないこと
		$v = $obj_validation->add('user_id', 'user_id');
		$v->add_rule('is_login'); // 独自ルール

		$login_dto = LoginDto::get_instance();
		$arr_valid_values = array(
				'user_id' => $login_dto->get_user_id(),
		);

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'is_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。すでにログインしています', 7012); // ログイン済
					default:
						if ($i === 'email' or $i === 'password')
						{
							static::$_arr_error[$i] = $error->get_message();
							return true;
						}
						else
						{
							throw new \Exception($error->get_message());
						}
				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_editregistexecute()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログインしていないこと
		$v = $obj_validation->add('user_id', 'user_id');
		$v->add_rule('is_login'); // 独自ルール

		$login_dto = LoginDto::get_instance();
		$arr_valid_values = array(
				'user_id' => $login_dto->get_user_id(),
		);

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'is_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。すでにログインしています', 7012); // ログイン済
					default:
						if ($i === 'email' or $i === 'password')
						{
							static::$_arr_error[$i] = $error->get_message();
							return true;
						}
						else
						{
							throw new \Exception($error->get_message());
						}
				} // endswitch
			}// endforeach
		}

		return true;
	}


	/**
	 * ログインしていないことを確認
	 */
	public static function _validation_not_login($user_id)
	{
		\Log::debug('[start]'. __METHOD__);

		if ( ! empty($user_id))
		{
			return false;
		}

		return true;
	}


	/**
	 * ログインしていることを確認
	 */
	public static function _validation_is_login($user_id)
	{
		\Log::debug('[start]'. __METHOD__);

		if (empty($user_id))
		{
			return false;
		}

		return true;
	}


	/**
	 *  Emailが登録されているグルーヴオンラインとして登録されていること
	 * @param string $email
	 */
	public static function _validation_is_exist_email($email)
	{
		\Log::debug('[start]'. __METHOD__);

		$auth_type = 'grooveonline';

		if (static::_is_exist_email($email, $auth_type, false))
		{
			return true;
		}

		return false;
	}

	/**
	 * 再発行申請メールアドレスがすでに存在しているかの確認
	 * APIへパスワード再発行テーブルに該当Emailがないことを確認（リロード禁止措置）
	 * @param unknown $email
	 * @return boolean
	 */
	public static function _validation_not_available_password_reissued_email($email)
	{
		\Log::debug('[start]'. __METHOD__);

		if (static::_is_exist_valid_email_at_password_reissue($email))
		{
			return false;
		}

		return true;
	}


	public static function set_dto_for_grooveonlinepassreissuesendmail($auth_type)
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto  = UserDto::get_instance();

		$user_dto->set_email(\Input::post('email'));
		$user_dto->set_auth_type(trim($auth_type));

		return true;
	}


	public static function set_dto_for_grooveonline()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto  = UserDto::get_instance();

		$user_dto->set_email(\Input::post('email'));
		$user_dto->set_password(\Input::post('password'));
		$user_dto->set_auth_type('grooveonline');

		return true;
	}


	public static function set_dto_for_grooveonlineregistexecute()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto = LoginDto::get_instance();
		$user_dto  = UserDto::get_instance();

		$login_dto->set_auth_type('grooveonline');
		$login_dto->set_login_hash(\Input::post('login_hash'));
		$login_dto->set_auto_login(\Input::post('auto_login'));
		$user_dto->set_user_id(\Input::post('user_id'));
		$user_dto->set_user_name(\Input::post('user_name'));
		$user_dto->set_first_name(\Input::post('first_name'));
		$user_dto->set_last_name(\Input::post('last_name'));
		$user_dto->set_password(\Input::post('password'));
		$user_dto->set_password_digits(\Input::post('password_digits'));
		$user_dto->set_email(\Input::post('email'));
		$user_dto->set_link(\Input::post('link'));
		$user_dto->set_gender(\Input::post('gender'));
		$user_dto->set_birthday(\Input::post('birthday'));
		$user_dto->set_birthday_year(\Input::post('birthday_year'));
		$user_dto->set_birthday_month(\Input::post('birthday_month'));
		$user_dto->set_birthday_day(\Input::post('birthday_day'));
		$user_dto->set_birthday_secret(\Input::post('secret'));
		$user_dto->set_old(\Input::post('old'));
		$user_dto->set_old_secret(\Input::post('old_secret'));
		$user_dto->set_locale(\Input::post('locale'));
		$user_dto->set_country(\Input::post('country'));
		$user_dto->set_postal_code(\Input::post('postal_code'));
		$user_dto->set_pref(\Input::post('pref'));
		$user_dto->set_locality(\Input::post('locality'));
		$user_dto->set_street(\Input::post('street'));
		$user_dto->set_profile_fields(\Input::post('profile_fields'));

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

		$user_dto->set_group(\Input::post('group'));
		$user_dto->set_oauth_id(\Input::post('oauth_id'));
		$user_dto->set_tmp_image_url(\Input::post('tmp_image_url'));
		$user_dto->set_picture(\Input::post('picture'));

		return true;
	}


	public static function set_user_info_to_dto_from_postrequest($auth_type)
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$arr_request = \Input::post();
		$arr_request['auth_type'] = $auth_type;

		$user_dto  = \user\model\dto\UserDto::get_instance();
		$login_dto = \login\model\dto\LoginDto::get_instance();

		$arr_userdto_class_method  = get_class_methods($user_dto);
		$arr_logindto_class_method = get_class_methods($login_dto);

		foreach ($arr_userdto_class_method as $method)
		{
			if (preg_match('/^set_(.+)/', $method, $match))
			{
				if (isset($arr_request[$match[1]]))
				{
					$dto_set_method = "set_". $match[1];
					$user_dto->$dto_set_method($arr_request[$match[1]]);
				}
			}
		}
		unset($method, $match, $dto_set_method);

		foreach ($arr_logindto_class_method as $method)
		{
			if (preg_match('/^set_(.+)/', $method, $match))
			{
				if (isset($arr_request[$match[1]]))
				{
					$dto_set_method = "set_". $match[1];
					$login_dto->$dto_set_method($arr_request[$match[1]]);
				}
			}
		}
		unset($method, $match, $dto_set_method);

		return true;
	}


	/**
	 * @return boolean
	 */
	public static function set_user_info_to_dto_from_api()
	{
		try
		{
			\Log::debug('[start]'. __METHOD__);

			$login_dto = LoginDto::get_instance();
			$user_dto  = UserDto::get_instance();
			$group_dto = GroupDto::get_instance();

			$arr_send = array(
				'user_id'    => $login_dto->get_user_id(),
				'auth_type'  => $login_dto->get_auth_type(),
				'login_hash' => $login_dto->get_login_hash(),
			);

			# CURL送信のための情報をDTOにセット
			$curl_dto = CurlDto::get_instance();
			$curl_dto->set_url(\Config::get('host.api_url'). 'main/user/me.json');
			$curl_dto->set_arr_send($arr_send);

			# CURLにてAPI送信
			$obj_api = new Api();
			$obj_api->send_curl();

			# CURL送信レスポンス
			$obj_response = $obj_api->get_curl_response();
			$arr_userdto_class_method = get_class_methods($user_dto);
			$arr_logindto_class_method = get_class_methods($login_dto);

			# UserDtoにセット
			foreach ($arr_userdto_class_method as $key => $method)
			{
				if (preg_match('/^set_(.+)/i', $method, $match))
				{
					if (isset($obj_response->$match[1]))
					{
						$user_dto->$method($obj_response->$match[1]);
					}
					if (isset($obj_response->result->$match[1]))
					{
						$user_dto->$method($obj_response->result->$match[1]);
					}
				}
			} // endforeach
			unset($key, $method, $match);

			# LoginDtoにセット
			foreach ($arr_logindto_class_method as $key => $method)
			{
				if (preg_match('/^set_(.+)/i', $method, $match))
				{
					if (isset($obj_response->$match[1]))
					{
						$login_dto->$method($obj_response->$match[1]);
					}
					if (isset($obj_response->result->$match[1]))
					{
						$login_dto->$method($obj_response->result->$match[1]);
					}
				}
			} // endforeach
			unset($key, $method, $match);

			# GroupDtoにセット
			$arr = $obj_response->result->group;
			$group_dto->set_group($arr);

			return true;

		}
		catch (\Exception $e)
		{
			var_dump($e->getMessage());
			var_dump($e->getFile().$e->getLine());
			throw new \Exception($e);
		}
	}


	/**
	 * セッションにユーザ情報を登録します。
	 * 自動ログインクッキーの制御を行う。
	 * 引数$is_updateフラグがtrueの場合はオートログインクッキーを操作しません
	 * @param boolean $is_first_regist 初回ログイン時（サンキュー文表示する用）
	 * @return boolean
	 */
	public static function set_user_info_to_session_from_dto($is_first_regist=false)
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto = LoginDto::get_instance();
		$user_dto  = UserDto::get_instance();
		$favorite_dto = FavoriteUserDto::get_instance();

		$arr_user_info = array(
			'user_id'    => $login_dto->get_user_id(),
			'user_name'  => $user_dto->get_user_name(),
			'login_hash' => $login_dto->get_login_hash(),
			'auth_type'  => $login_dto->get_auth_type(),
			'oauth_id'   => $user_dto->get_oauth_id(),
			'auto_login' => $login_dto->get_auto_login(),
			'is_first_regist' => $is_first_regist
		);

		SessionService::set('user_info', $arr_user_info);

		$arr_favorite_users = array();
		$arr_get_favorite_users = $favorite_dto->get_favorite_users();
		if ( ! empty($arr_get_favorite_users))
		{
			foreach ($arr_get_favorite_users as $i => $val)
			{
				$arr_favorite_users[$i] = $val;
			}
		}

		SessionService::set('favorite_users', $arr_favorite_users);

		return true;
	}


	/**
	 *
	 * @param int $time (nullの場合は最長継続値になります)
	 * @param string $time
	 * @return boolean
	 */
	public static function set_auto_login_to_session()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		\Log::info('セッションにuser_info.auto_login値にtrueを設定');
		SessionService::set('user_info.auto_login', '1');

		return true;
	}


	/**
	 * sessionからユーザログイン情報を取得しDTOにセット
	 */
	public static function set_user_info_to_dto_from_session()
	{
		\Log::debug('[start]'. __METHOD__);

		# セッションからログインユーザ情報を取得
		$arr_user_info = static::_get_user_info_from_session();

		$login_dto = LoginDto::get_instance();
		$user_dto  = UserDto::get_instance();

		$arr_logindto_class_method = get_class_methods($login_dto);
		$arr_userdto_class_method  = get_class_methods($user_dto);

		foreach ($arr_logindto_class_method as $method)
		{
			if (preg_match('/^set_(.+)/', $method, $match))
			{
				if ( ! empty($arr_user_info[$match[1]]))
				{
					$dto_set_method = "set_". $match[1];
					$login_dto->$dto_set_method($arr_user_info[$match[1]]);
				}
			}
		}
		unset($method, $match, $dto_set_method);

		foreach ($arr_userdto_class_method as $method)
		{
			if (preg_match('/^set_(.+)/', $method, $match))
			{
				if ( ! empty($arr_user_info[$match[1]]))
				{
					$dto_set_method = "set_". $match[1];
					$user_dto->$dto_set_method($arr_user_info[$match[1]]);
				}
			}
		}
		unset($method, $match, $dto_set_method);
	}


	public static function set_auto_login_info_to_dto_from_session()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$login_dto = LoginDto::get_instance();

		# セッションからauto_login情報を取得しdtoにセット
		$login_dto->set_auto_login(static::_get_auto_login_info_from_session());

		return true;
	}


	public static function set_auth_type_to_dto($auth_type)
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$login_dto = LoginDto::get_instance();
		$user_dto  = UserDto::get_instance();

		$login_dto->set_auth_type($auth_type);
		$user_dto->set_auth_type($auth_type);

		return true;
	}

	public static function check_session_expired($expired)
	{
		\Log::debug('[start]'. __METHOD__);

		# セッション情報の有効期限を精査する
		if (property_exists(\Session::get('tmp_review', new \stdClass()), 'timestamp'))
		{
			// ログイン前レビュー
			if (\Date::forge()->get_timestamp() > \Session::get('tmp_review')->timestamp + $expired)
			{
				\Session::delete('tmp_review');
			}
		}

		# 一時画像
		if (property_exists(\Session::get('tmp_image', new \stdClass()), 'timestamp'))
		{
			// ログイン画像一時パス
			if (\Date::forge()->get_timestamp() > \Session::get('tmp_image')->timestamp + $expired)
			{
				\Session::delete('tmp_image');
			}
		}

		# 遷移元アドレス
		if (property_exists(\Session::get('from', new \stdClass()), 'timestamp'))
		{
			// ログイン後の遷移url
			if (\Date::forge()->get_timestamp() > \Session::get('from')->timestamp + $expired)
			{
				\Session::delete('from');
			}
		}

		# アーティスト検索
		if (property_exists(\Session::get('search_artist', new \stdClass()), 'timestamp'))
		{
			// ログイン後の遷移url
			if (\Date::forge()->get_timestamp() > \Session::get('search_artist')->timestamp + $expired)
			{
				\Session::delete('search_artist');
			}
		}

		return true;
	}

	/**
	 * 招待ユーザのリクエストパラメータをセッションへ格納
	 * @return boolean
	 */
	public static function set_session_for_invited()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		\Session::set_flash('user_regist_target_id', \Input::param('target_id')); // 招待するユーザID
		\Session::set_flash('user_regist_invite_id', \Input::param('invite_id')); // 招待したユーザID
		\Session::set_flash('user_regist_group_id', \Input::param('group_id'));   // グループID
		\Session::set_flash('invited_by', \Input::param('invited_by'));           // 招待種別 group

		return true;
	}

	/**
	 * パラメータをセット
	 */
	public static function make_parameter()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$arr_parameter = array(
			'invited_by' => \Input::param('invited_by'),
			'group_id'   => \Input::param('group_id'),
			'target_id'  => \Input::param('target_id'),
			'invite_id'  => \Input::param('invite_id'),
		);

		return http_build_query($arr_parameter);
	}


	public static function is_exists_email()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);
	}


	public static function is_invited(array $arr_profile)
	{
		\Log::debug('[start]'. __METHOD__);

		try
		{
			if ($arr_profile['invite_id'] === $arr_profile['target_id'])
			{
				return false;
			}

			$arr_send = array(
				'user_id'	 => $arr_profile['user_id'],
				'invited_by' => $arr_profile['invited_by'],
				'group_id'	 => $arr_profile['group_id'],
				'invite_id'	 => $arr_profile['invite_id'],
				'target_id'	 => $arr_profile['target_id'],
			);

			# CURL送信のための情報をDTOにセット
			$curl_dto = CurlDto::get_instance();
			$curl_dto->set_url(\Config::get('host.api_url'). 'main/group/isinvited.json');
			$curl_dto->set_arr_send($arr_send);

			# CURLにてAPI送信
			$obj_api = new Api();
			$obj_api->send_curl();

			# CURL送信レスポンス
			$obj_response = $obj_api->get_curl_response();

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			return false;
		}
	}


	public static function is_auto_login()
	{
		\Log::debug('[start]'. __METHOD__);

		$arr_user_info = SessionService::get('auto_login');

		if (empty($arr_user_info['auto_login']))
		{
			return false;
		}
		return true;
	}


	/**
	 * ユーザプロフィールをAPIサーバに送信し登録します
	 * 存在すれば更新登録になります。
	 * 返却されたlogin_hash値をlogin_dtoにセットする。
	 *
	 * @param array $arr_profile
	 * @throws \Exception
	 * @return multitype:NULL
	 */
	public static function send_profile_to_api_undecide()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto  = UserDto::get_instance();
		$login_dto = LoginDto::get_instance();
		$group_dto = GroupDto::get_instance();

		$arr_send = array();
		$arr_send['user_id']         = $user_dto->get_user_id();
		$arr_send['user_name']       = $user_dto->get_user_name();
		$arr_send['first_name']      = $user_dto->get_first_name();
		$arr_send['last_name']       = $user_dto->get_last_name();
		$arr_send['password']        = $user_dto->get_password();
		$arr_send['password_digits'] = $user_dto->get_password_digits();
		$arr_send['email']           = $user_dto->get_email();
		$arr_send['link']            = $user_dto->get_link();
		$arr_send['gender']          = $user_dto->get_gender();
		$arr_send['birthday']        = $user_dto->get_birthday();
		$arr_send['birthday_year']   = $user_dto->get_birthday_year();
		$arr_send['birthday_month']  = $user_dto->get_birthday_month();
		$arr_send['birthday_day']    = $user_dto->get_birthday_day();
		$arr_send['birthday_secret'] = $user_dto->get_birthday_secret();
		$arr_send['old']             = $user_dto->get_old();
		$arr_send['old_secret']      = $user_dto->get_old_secret();
		$arr_send['locale']          = $user_dto->get_locale();
		$arr_send['country']         = $user_dto->get_country();
		$arr_send['postal_code']     = $user_dto->get_postal_code();
		$arr_send['pref']            = $user_dto->get_pref();
		$arr_send['locality']        = $user_dto->get_locality();
		$arr_send['street']          = $user_dto->get_street();
		$arr_send['profile_fields']  = $user_dto->get_profile_fields();
		$arr_send['facebook_url']    = $user_dto->get_facebook_url();
		$arr_send['twitter_url']     = $user_dto->get_twitter_url();
		$arr_send['google_url']      = $user_dto->get_google_url();
		$arr_send['instagram_url']   = $user_dto->get_instagram_url();
		$arr_send['site_url']        = $user_dto->get_site_url();
		$arr_send['group']           = $user_dto->get_group();
		$arr_send['oauth_id']        = $user_dto->get_oauth_id();
		$arr_send['tmp_image_url']   = $user_dto->get_tmp_image_url();
		$arr_send['picture']         = $user_dto->get_picture();
		$arr_send['member_type']     = 1;
		$arr_send['login_hash']      = $login_dto->get_login_hash();
		$arr_send['auth_type']       = $login_dto->get_auth_type();
		$arr_send['auto_login']      = $login_dto->get_auto_login();

		if (empty($arr_send['birthday_year']))
		{
			$arr_send['birthday_year'] = \Date::forge()->format('%Y') - $arr_send['old'];
		}
		if (isset($arr_send['old_secret']) && $arr_send['old_secret'] == 'checked')
		{
			$arr_send['old_secret'] = 1;
		}

		# 招待ユーザ情報を代入
		$arr_send['invited_by'] = \Session::get_flash('invited_by');			// 招待種別
		$arr_send['invite_id']  = \Session::get_flash('user_regist_invite_id');	// 招待者ユーザID
		$arr_send['group_id']   = \Session::get_flash('user_regist_group_id');	// グループID
		$user_regist_target_id  = \Session::get_flash('user_regist_target_id');
		if (isset($user_regist_target_id))
		{
			$arr_send['target_id']  = \Session::get_flash('user_regist_target_id');	// 招待するメンバーのユーザID
			$arr_send['user_id']    = \Session::get_flash('user_regist_target_id');	// override
		}

		if (isset($arr_send['invited_by']))
		{
			# 招待ユーザがテーブルに存在しない場合は新規登録とする
			if (false === static::is_invited($arr_send))
			{
				unset($arr_send['user_id']);
				unset($arr_send['invited_by']);
				unset($arr_send['invite_id']);
				unset($arr_send['group_id']);
				unset($arr_send['target_id']);
			}
		}

		if (empty($arr_send['user_id']))
		{
			$url = \Config::get('host.api_url'). 'main/user/regist.json';
		}
		else
		{
			$url = \Config::get('host.api_url'). 'main/user/edit.json';
		}

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url($url);
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		if ( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiレスポンスが不正です');
		}

		# dtoにユーザID, login_hashを格納
		$login_dto->set_user_id($obj_response->result->user_id);
		$login_dto->set_login_hash($obj_response->result->login_hash);

		return true;
	}



	public static function send_decide()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto = LoginDto::get_instance();
		$arr_send = array(
			'user_id'    => $login_dto->get_user_id(),
			'auth_type'  => $login_dto->get_auth_type(),
			'login_hash' => $login_dto->get_login_hash(),
		);

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/user/decide.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		if ( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiレスポンスが不正です');
		}

		# dtoにユーザID, login_hashを格納
		$login_dto->set_user_id($obj_response->result->user_id);
		$login_dto->set_login_hash($obj_response->result->login_hash);

		return true;
	}


	/**
	 * 一時パスワードをapiから発行してもらう
	 *
	 * @return boolean
	 */
	public static function send_password_reissue()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto = LoginDto::get_instance();
		$user_dto  = UserDto::get_instance();

		$arr_send = array(
			'email'     => $user_dto->get_email(),
			'auth_type' => $user_dto->get_auth_type(),
		);

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/user/passwordreissuerequest.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		if ( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiレスポンスが不正です');
		}

		$user_dto->set_user_id($obj_response->result->tentative_id);
		$user_dto->set_email($obj_response->result->email);
		$user_dto->set_password($obj_response->result->tentative_password);
		$login_dto->set_passreissue_expired_min($obj_response->result->expired_min);

		return false;

	}


	/**
	 * userのログインパスワードを変更する
	 */
	public static function send_password_update()
	{
		\Log::debug('[start]'. __METHOD__);

		try
		{
			$arr_send = array(
				'auth_type'          => 'grooveonline',
				'email'              => \Input::post('email'),
				'tentative_id'       => \Input::post('tentative_id'),
				'tentative_password' => \Input::post('tentative_password'),
				'password'           => \Input::post('password'),
			);

			# CURL送信のための情報をDTOにセット
			$curl_dto = CurlDto::get_instance();
			$curl_dto->set_url(\Config::get('host.api_url'). 'main/user/passwordreissueupdate.json');
			$curl_dto->set_arr_send($arr_send);

			# CURLにてAPI送信
			$obj_api = new Api();
			$obj_api->send_curl();

			# CURL送信レスポンス
			$obj_response = $obj_api->get_curl_response();
			if ( ! property_exists($obj_response, 'result'))
			{
				throw new \Exception('apiレスポンスが不正です');
			}

			return true;
		}
		catch (\Exception $e)
		{
			if ($e->getCode() == 9001)
			{
				// リダイレクト
				$tx = 'パスワード変更の有効時間が切れました。恐れ入りますが再度変更手続きを行ってください';
				\Log::error($tx);
				$arr_error['password'] = $tx;
				\Session::set_flash('arr_error', $arr_error);
				\Response::redirect('/login/');
			}

			throw new \Exception($e);
		}
	}


	/**
	 * ユーザIDとログインハッシュでユーザ情報を取得します
	 * 返却された新login_hash、user_id, user_nameをLoginDtoに格納
	 * @return multitype:|multitype:unknown
	 */
	public static function login_api()
	{
		\Log::debug('[start]'. __METHOD__);

		try
		{
			$user_dto     = UserDto::get_instance();
			$login_dto    = LoginDto::get_instance();
			$favorite_dto = FavoriteUserDto::get_instance();

			if ($user_dto->get_auth_type() == 'grooveonline')
			{
				$arr_send['email' ]     = $user_dto->get_email();
				$arr_send['password']   = $user_dto->get_password();
				$arr_send['auth_type']  = $user_dto->get_auth_type();
				\Session::set('email', $user_dto->get_email());
			}
			else
			{
				$arr_send['oauth_id']  = $user_dto->get_oauth_id();
				$arr_send['auth_type'] = $user_dto->get_auth_type();
			}

			# CURL送信のための情報をDTOにセット
			$curl_dto = CurlDto::get_instance();
			$curl_dto->set_url(\Config::get('host.api_url'). 'main/login/login.json');
			$curl_dto->set_arr_send($arr_send);

			# CURLにてAPI送信
			$obj_api = new Api();
			$obj_api->send_curl();

			# CURL送信レスポンス
			$obj_response = $obj_api->get_curl_response();
			if ( ! property_exists($obj_response, 'result'))
			{
				throw new \Exception('apiレスポンスが不正です');
			}
			if ( ! property_exists($obj_response, 'success'))
			{
				if ( ! $obj_response->success)
				{
					return false;
				}
			}

			$login_dto->set_user_id($obj_response->result->user_id);
			$login_dto->set_user_name($obj_response->result->user_name);
			$login_dto->set_login_hash($obj_response->result->login_hash);
			$user_dto->set_user_name($obj_response->result->user_name);
			$favorite_dto->set_favorite_users($obj_response->result->favorite_users);

			return true;
		}
		catch (\Exception $e)
		{
			if ($e->getCode() === 9001)
			{
				\Log::debug('ユーザ情報の取得に失敗しました');
				return false;
			}
			throw new \Exception($e);
		}
	}

	public static function logout()
	{
		try
		{
			\Log::debug('[start]'. __METHOD__);

			$login_dto = LoginDto::get_instance();

			# APIにログアウト情報を送信します
			$arr_send = array(
				'user_id'	 => $login_dto->get_user_id(),
				'login_hash' => $login_dto->get_login_hash(),
				'auth_type'  => $login_dto->get_auth_type(),
			);

			# CURL送信のための情報をDTOにセット
			$curl_dto = CurlDto::get_instance();
			$curl_dto->set_url(\Config::get('host.api_url'). 'main/login/logout.json');
			$curl_dto->set_arr_send($arr_send);

			# CURLにてAPI送信
			$obj_api = new Api();
			$obj_api->send_curl();

			# CURL送信レスポンス
			$obj_response = $obj_api->get_curl_response();
			if (empty($obj_response->success))
			{
				\Log::error('APIログアウト処理が失敗しました。'. $obj_response->code);
			}

			return true;
		}
		catch (\Exception $e)
		{
			if ($e->getCode() === 9001)
			{
				\Log::error('APIログアウト処理が失敗しました');
				return false;
			}

			throw new \Exception($e->getMessage(), $e->getCode());
		}
	}


	public static function clear_dto()
	{
		\Log::debug('[start]'. __METHOD__);

		# dto情報をクリアします。

		$login_dto = LoginDto::get_instance();
		$user_dto  = UserDto::get_instance();

		$login_dto_method = get_class_methods($login_dto);
		$user_dto_method  = get_class_methods($user_dto);

		foreach ($login_dto_method as $method)
		{
			if (preg_match('/^set_/', $method, $match))
			{
				$login_dto->$method(null);

			}
		}
		unset($method, $match);

		foreach ($user_dto_method as $method)
		{
			if (preg_match('/^set_[.]+$/', $method, $match))
			{
				$method(null);
			}
		}
		unset($method, $match);

		return true;
	}


	public static function session_destroy()
	{
		\Log::debug('[start]'. __METHOD__);

		# セッション情報を削除します
		SessionService::delete('user_info');     // ログインユーザ情報
		SessionService::delete('tmp_image');     // 一時画像
		SessionService::delete('search_artist'); // 検索アーティスト名
		SessionService::delete('favorite_users'); // お気に入りユーザー

		return true;
	}


	/**
	 * セッションにリダイレクト先urlが存在した場合にリダイレクトURLを返す // @todo
	 */
	public static function get_redirect_url_from_session()
	{
		\Log::debug('[start]'. __METHOD__);

		$expire_days = 1; // 1days

		$obj_from = \Session::get('from', new \stdClass());
		if ( ! property_exists($obj_from, 'url'))
		{
			return false;
		}

		$now_timestamp = \Date::forge()->get_timestamp();
		// 1day以上経過した場合
		if ($now_timestamp > $obj_from->timestamp + (60 * 60 * 24))
		{
			\Session::delete('from');
			return false;
		}
		else
		{
			\Session::delete('from');
			$redirect_url = $obj_from->url;

			return $redirect_url;
		}
	}


	public static function get_image_profile_url($user_id, $login_hash, $https=false, $size=126)
	{
		return static::_get_user_image_url($user_id, $login_hash, $https, $size);
	}

	public static function get_user_image_url_small($user_id, $https=false)
	{
		return static::_get_user_image_url($user_id, null, $https, '34');
	}

	public static function get_user_image_url_medium($user_id, $https=false)
	{
		return static::_get_user_image_url($user_id, null, $https, '64');
	}

	public static function get_user_image_url_large($user_id, $https=false)
	{
		return static::_get_user_image_url($user_id, null, $https, '126');
	}

	public static function get_user_image_url_extralarge($user_id, $https=false)
	{
		return static::_get_user_image_url($user_id, null, $https, '252');
	}





	private static function _get_user_image_url($user_id, $login_hash=null, $https=false, $size=126)
	{
		$obj_image = new util\Image();

		if ( ! is_null($login_hash))
		{
			$hash6 = "?". substr($login_hash, 0, 6);
		}
		else
		{
			$hash6 = '';
		}

		if ($https)
		{
			$image_url = \Config::get('host.img_url_https'). 'profile/user/'. $user_id. '/'. $size. '/'. md5($user_id). '.jpg'. $hash6;
			//$image_url = \Config::get('host.img_local_url'). 'profile'. DS. 'user'. DS. $user_id. DS. $size. DS. md5($user_id). '.jpg'. $hash6;
		}
		else
		{
			$image_url = \Config::get('host.img_url_http'). 'profile/user/'. $user_id. '/'. $size. '/'. md5($user_id). '.jpg'. $hash6;
			//$image_url = \Config::get('host.img_local_url'). 'profile'. DS. 'user'. DS. $user_id. DS. $size. DS. md5($user_id). '.jpg'. $hash6;
		}
		return $image_url;
	}


	private static function _get_auto_login_info_from_session()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$obj_session = new \login\domain\service\SessionService();
		return $obj_session->get('auto_login', 0);
	}


	/**
	 * メールアドレスが未存在であることを確認する
	 * 未存在：true, 存在する：false
	 * (更新時はメールアドレスに変更がなければtrueを返す)
	 *
	 * @param unknown $email
	 * @param unknown $auth_type
	 * @param string $is_update
	 * @return boolean
	 */
	private static function _is_not_exist_email($email, $auth_type, $is_update=false)
	{
		\Log::debug('[start]'. __METHOD__);

		try
		{
			$user_dto = UserDto::get_instance();

			# 更新時はメールアドレスが変更になった場合のみチェックを行います。
			if ($is_update)
			{
				LoginService::set_user_info_to_dto_from_api();
				if ($email == $user_dto->get_email())
				{
					return true;
				}
			}

			$arr_send = array(
					'email'   => $email,
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


	private static function _is_exist_email($email, $auth_type, $is_update=false)
	{
		\Log::debug('[start]'. __METHOD__);

		try
		{
			$user_dto = UserDto::get_instance();

			# 更新時はメールアドレスが変更になった場合のみチェックを行います。
			if ($is_update)
			{
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
			$curl_dto->set_url(\Config::get('host.api_url'). 'main/user/isregistemail.json');
			$curl_dto->set_arr_send($arr_send);

			# CURLにてAPI送信
			$obj_api = new Api();
			$obj_api->send_curl();

			# CURL送信レスポンス
			$obj_response = $obj_api->get_curl_response(); // success=>falseの場合は9001コード

			if ($obj_response->result->is_exist == false)
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


	/**
	 * すでにパスワード再設定を行い有効な状態のユーザであるか
	 * @param unknown $email
	 * @throws \Exception
	 * @return boolean 有効:true, 無効:false
	 */
	private static function _is_exist_valid_email_at_password_reissue($email)
	{
		\Log::debug('[start]'. __METHOD__);

		try
		{
			$arr_send = array(
				'email' => $email,
			);

			# CURL送信のための情報をDTOにセット
			$curl_dto = CurlDto::get_instance();
			$curl_dto->set_url(\Config::get('host.api_url'). 'main/user/isexistemailatpasswordreissue.json');
			$curl_dto->set_arr_send($arr_send);

			# CURLにてAPI送信
			$obj_api = new Api();
			$obj_api->send_curl();

			# CURL送信レスポンス
			$obj_response = $obj_api->get_curl_response();
			$login_dto = LoginDto::get_instance();
			$login_dto->set_passreissue_expired_min($obj_response->result->expired_min);

			if ($obj_response->result->is_exist == false)
			{
				return false;
			}

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getCode());

			throw new \Exception($e);
		}
	}

}