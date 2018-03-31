<?php

class View_Login_Grooveonlineregistselect extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$this->login_error = "";
		$this->mailaddress = "";
		$this->password = "";

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}
}