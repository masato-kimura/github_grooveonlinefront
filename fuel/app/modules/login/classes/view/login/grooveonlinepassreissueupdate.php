<?php

class View_Login_Grooveonlinepassreissueupdate extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$this->login_error = \Session::get_flash('login_error');
		$this->email = \Input::param('email');

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}
}