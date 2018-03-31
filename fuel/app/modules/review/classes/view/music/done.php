<?php

class View_Music_Done extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$login_dto = \login\model\dto\LoginDto::get_instance();

		$this->login_error = \Session::get_flash('login_error');
		$email = \Session::get('email');
		if (empty($email))
		{
			$email = \Session::get_flash('email');
			if (empty($email))
			{
				$email = null;
			}
		}

		$this->email = $email;
		$this->password = "";
		$this->auto_login = $login_dto->get_auto_login();
		$this->from_password_reissue_caption = \Session::get_flash('from_password_reissue_caption');

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}
}