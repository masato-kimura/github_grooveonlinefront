<?php
class View_Login_Grooveonlineregistconfirm extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$this->login_error = "";
		$this->email = "";
		$this->password = "";

		$old = Input::post('old');
		if ( ! empty($old))
		{
			$birthday_year = Date::forge()->format('%Y') - Input::post('old');
		}
		else
		{
			$birthday_year = "";
		}

		$this->user_name       = Input::post('user_name');
		$this->email           = Input::post('email');
		$this->password        = Input::post('password');
		$this->gender          = Input::post('gender');
		$this->old             = Input::post('old');
		$this->old_secret      = Input::post('old_secret');
		$this->birthday_year   = $birthday_year;
		$this->birthday_month  = Input::post('birthday_month');
		$this->birthday_day    = Input::post('birthday_day');
		$this->birthday_secret = Input::post('birthday_secret');
		$this->pref            = Input::post('pref');
		$this->profile_fields  = Input::post('profile_fields');
		$this->facebook_url    = Input::post('facebook_url');
		$this->google_url      = Input::post('google_url');
		$this->twitter_url     = Input::post('twitter_url');
		$this->instagram_url   = Input::post('instagram_url');
		$this->site_url        = Input::post('site_url');
		$this->auth_type       = Input::post('auth_type');
		$this->oauth_id        = Input::post('oauth_id');
		$this->picture_url     = Input::post('picture_url');
		$this->auto_login      = Input::post('auto_login');

		if (empty($this->arr_error))
		{
			$this->error_image = null;
		}
		else
		{
			$this->error_image = $this->arr_error[0];
		}

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}
}