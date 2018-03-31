<?php

use user\model\dto\UserDto;
use login\model\dto\LoginDto;
class View_Login_Grooveonlinepassreissuesendmail extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto  = UserDto::get_instance();
		$login_dto = LoginDto::get_instance();

		$this->email = $user_dto->get_email();
		$this->passreissue_expired_min = $login_dto->get_passreissue_expired_min();

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}
}