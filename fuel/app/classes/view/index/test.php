<?php

class View_Index_Test extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);

		return true;
	}
}
