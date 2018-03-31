<?php

use login\model\dto\LoginDto;

class View_Login_Grooveonlinepassreissuerequest extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto = LoginDto::get_instance();

		$this->arr_error = isset($this->arr_error)? $this->arr_error: array();
		$this->error_message = isset($this->error_message)? $this->error_message: null;
		$this->email = \Input::param('email');
		$this->passreissue_expired_min = $login_dto->get_passreissue_expired_min();

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}
}