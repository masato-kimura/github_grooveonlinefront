<?php
use login\domain\service;
class View_Login_Editregistconfirm extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$this->login_error = "";
		$this->user_name       = trim(Input::post('user_name'));
		$this->email           = trim(Input::post('email'));
		$this->password        = trim(Input::post('password'));
		$this->gender          = Input::post('gender', null);
		$this->birthday_year   = Input::post('birthday_year');
		$this->birthday_month  = Input::post('birthday_month');
		$this->birthday_day    = Input::post('birthday_day');
		$this->birthday_secret = Input::post('birthday_secret');
		$this->old             = Input::post('old');
		$this->old_secret      = Input::post('old_secret');
		$this->profile_fields  = Input::post('profile_fields');
		$this->facebook_url    = trim(Input::post('facebook_url'));
		$this->google_url      = trim(Input::post('google_url'));
		$this->twitter_url     = trim(Input::post('twitter_url'));
		$this->instagram_url   = trim(Input::post('instagram_url'));
		$this->site_url        = trim(Input::post('site_url'));
		$this->pref            = Input::post('pref');
		$this->auth_type       = Input::post('auth_type');
		$this->oauth_id        = Input::post('oauth_id');

		switch ($this->gender)
		{
			case 'male':
			case 'men':
				$this->gender_disp = '男性';
				break;

			case 'female':
			case 'woman':
				$this->gender_disp = '女性';
				break;

			case 'secret':
				$this->gender_disp = '非公開';
				break;

			default:
				$this->gender_disp = null;
		}

		// ログイン再確認セッション有効時はメールアドレス全表示
		if (\Session::get('available_login'))
		{
			$this->email_disp = $this->email;
		}
		else
		{
			if ($this->auth_type === 'grooveonline')
			{
				$this->email_disp = $this->_conversion_for_email_display($this->email);
			}
			else
			{
				$this->email_disp = $this->email;
			}
		}

		$login_dto = \login\model\dto\LoginDto::get_instance();

		$this->user_image
			= service\LoginService::get_image_profile_url($login_dto->get_user_id(), $login_dto->get_login_hash(), true);
		$this->error_img
			= isset($this->arr_error['img'][0]['message']) ? $this->arr_error['img'][0]['message'] : null;
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

		$this->error_image = '';

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