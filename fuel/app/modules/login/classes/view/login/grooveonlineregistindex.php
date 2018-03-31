<?php
use user\model\dto\UserDto;
use login\model\dto\LoginDto;

class View_Login_Grooveonlineregistindex extends ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto  = UserDto::get_instance();
		$login_dto = LoginDto::get_instance();

		$this->user_name       = Input::post('user_name', $user_dto->get_user_name());
		$this->email           = Input::post('email', $user_dto->get_email());
		$this->password        = Input::post('password', $user_dto->get_password());
		$this->gender          = Input::post('gender', $user_dto->get_gender());
		$this->birthday        = Input::post('birthday', $user_dto->get_birthday());
		$this->birthday_year   = Input::post('birthday_year', $user_dto->get_birthday_year());
		$this->birthday_month  = Input::post('birthday_month', $user_dto->get_birthday_month());
		$this->birthday_day    = Input::post('birthday_day', $user_dto->get_birthday_day());
		$this->birthday_secret = Input::post('birthday_secret', $user_dto->get_birthday_secret());
		$this->old             = Input::post('old', $user_dto->get_old());
		$this->old_secret      = Input::post('old_secret', $user_dto->get_old_secret());
		$this->locale          = Input::post('locale', $user_dto->get_locale());
		$this->profile_fields  = Input::post('profile_fields', $user_dto->get_profile_fields());
		$this->facebook_url    = Input::post('facebook_url', $user_dto->get_facebook_url());
		$this->twitter_url     = Input::post('twitter_url', $user_dto->get_twitter_url());
		$this->google_url      = Input::post('google_url', $user_dto->get_google_url());
		$this->instagram_url   = Input::post('instagram_url', $user_dto->get_instagram_url());
		$this->site_url        = Input::post('site_url', $user_dto->get_site_url());
		$this->pref            = Input::post('pref', $user_dto->get_pref());
		$this->locality        = Input::post('locality', $user_dto->get_locality());
		$this->street          = Input::post('street', $user_dto->get_street());
		$this->group           = Input::post('group', $user_dto->get_group());
		$this->oauth_id        = Input::post('oauth_id', $user_dto->get_oauth_id());
		$this->picture_url     = Input::post('picture_url', $user_dto->get_picture());
		$this->auth_type       = Input::post('auth_type', $login_dto->get_auth_type());
		$this->auto_login      = Input::post('auto_login', $login_dto->get_auto_login());

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
		foreach (Config::get('pref.name') as $val) {
			$this->arr_pref[$val] = $val;
		}

		return true;
	}


}