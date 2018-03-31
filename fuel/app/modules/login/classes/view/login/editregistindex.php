<?php

use login\domain\service\LoginService;
use user\model\dto\UserDto;
use login\model\dto\LoginDto;
use group\model\dto\GroupDto;

class View_Login_Editregistindex extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto  = UserDto::get_instance();
		$login_dto = LoginDto::get_instance();
		$group_dto = GroupDto::get_instance();

		# パスワードアスタリスク表示のため
		$password_astarisk = "";
		$password_digits = $user_dto->get_password_digits();
		if ( ! empty($password_digits))
		{
			for($i=0; $i<$user_dto->get_password_digits(); $i++)
			{
				$password_astarisk .= "*";
			}
		}

		$this->user_id           = $user_dto->get_user_id();
		$this->user_name         = Input::post('user_name', $user_dto->get_user_name());
		$this->email             = Input::post('email', $user_dto->get_email());
		$this->password          = Input::post('password', $password_astarisk);
		$this->password_astarisk = $password_astarisk;
		$this->gender            = Input::post('gender', $user_dto->get_gender());
		$this->birthday_year     = Input::post('birthday_year', $user_dto->get_birthday_year());
		$this->birthday_month    = Input::post('birthday_month', $user_dto->get_birthday_month());
		$this->birthday_day      = Input::post('birthday_day', $user_dto->get_birthday_day());
		$this->birthday_secret   = Input::post('birthday_secret', $user_dto->get_birthday_secret());
		$this->ymd               = $user_dto->get_birthday_year(). sprintf('%02d', $user_dto->get_birthday_month()). sprintf('%02d', $user_dto->get_birthday_day());
		$this->old               = (int)((Date::forge()->format('%Y%m%d') - $this->ymd)/10000);
		$this->old_secret        = Input::post('old_secret', $user_dto->get_old_secret());
		$this->profile_fields    = Input::post('profile_fields', $user_dto->get_profile_fields());
		$this->facebook_url      = Input::post('facebook_url', $user_dto->get_facebook_url());
		$this->twitter_url       = Input::post('twitter_url', $user_dto->get_twitter_url());
		$this->google_url        = Input::post('google_url', $user_dto->get_google_url());
		$this->instagram_url     = Input::post('instagram_url', $user_dto->get_instagram_url());
		$this->site_url          = Input::post('site_url', $user_dto->get_site_url());
		$this->link              = Input::post('link', $user_dto->get_link());
		$this->pref              = Input::post('pref', $user_dto->get_pref());
		$this->auth_type         = Input::post('auth_type', $user_dto->get_auth_type());
		$this->oauth_id          = Input::post('oauth_id', $user_dto->get_oauth_id());
		$this->picture_url       = Input::post('picture_url', $user_dto->get_picture_url());
		$this->user_image        =  LoginService::get_image_profile_url($login_dto->get_user_id(), $login_dto->get_login_hash(), true);

		// group情報 (array_object)
		$this->group            = $group_dto->get_group();

		// 表示用
		$this->high_level_disp_flg = \Session::get('available_login', false);

		$this->email_convert    = $this->_conversion_for_email_display($this->email);

		$this->error_image
			= isset($this->arr_error['image'][0]['message']) ? $this->arr_error['image'][0]['message'] : null;
		$this->error_user_name
			= isset($this->arr_error['user_name']) ? $this->arr_error['user_name'] : null;
		$this->error_email
			= isset($this->arr_error['email']) ? $this->arr_error['email'] : null;
		$this->error_password
			= isset($this->arr_error['password']) ? $this->arr_error['password'] : null;
		$this->error_gender
			= isset($this->arr_error['gender']) ? $this->arr_error['gender'] : null;
		$this->error_old
			= isset($this->arr_error['old']) ? $this->arr_error['old'] : null;
		$this->error_birthday
			= isset($this->arr_error['birthday']) ? $this->arr_error['birthday'] : null;
		$this->error_profile_fields
			= isset($this->arr_error['profile_fields']) ? $this->arr_error['profile_fields'] : null;

		// 月
		$this->arr_birthday_month = array(0 => '月：');
		for ($i=1; $i<=12; $i++)
		{
			$this->arr_birthday_month[$i] = $i. '月';
		}

		// 日
		$this->arr_birthday_day = array(0 => '日：');
		for ($i=1; $i<=31; $i++)
		{
			$this->arr_birthday_day[$i] = $i. '日';
		}

		// 年齢
		$start_old = 3;
		$end_old = 120;
		$this->arr_old = array(0 => '年齢：');
		for ($i=$start_old; $i<$end_old; $i++)
		{
			$this->arr_old[$i] = $i. '才';
		}

		// 都道府県
		$this->arr_pref = array(0 => '都道府県：');
		foreach (Config::get('pref.name') as $val)
		{
			$this->arr_pref[$val] = $val;
		}

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}


	private function _conversion_for_email_display($tx)
	{
		if (empty($tx))
		{
			return null;
		}

		$out = preg_replace('/^(.{1,2})(.*)(@.*)$/i', '$1*****$3', $tx);

		return $out;
	}









}