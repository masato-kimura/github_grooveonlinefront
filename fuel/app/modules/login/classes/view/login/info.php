<?php

class View_Login_Info extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_from = \Session::get('from', new stdClass());
		if (property_exists($obj_from, 'url'))
		{
			$this->back_page = \Session::get('from')->url;
		}
		else
		{
			$this->back_page = '/';
		}

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}
}