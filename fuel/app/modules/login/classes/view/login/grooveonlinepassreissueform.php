<?php

class View_Login_Grooveonlinepassreissueform extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$this->email = \Input::param('email');

		// エラー処理
		if (empty($this->arr_error))
		{
			$this->arr_error = array();
			$this->arr_error['password'] = null;
		}
		$this->arr_error = array_merge($this->arr_error, \Session::get_flash('arr_error', array()));

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}
}