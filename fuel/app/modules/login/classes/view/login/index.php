<?php
class View_Login_Index extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto = \login\model\dto\LoginDto::get_instance();

		$this->password = "";

		$this->auto_login = $login_dto->get_auto_login();

		// 再入力用メールアドレス
		$email = \Session::get_flash('email', null);
		if (isset($email))
		{
			$this->email = $email;
		}

		// エラー関連
		$arr_session_error = \Session::get_flash('arr_error', array());
		$arr_error = isset($this->arr_error)? $this->arr_error: array();
		$this->arr_error = array_merge($arr_error, $arr_session_error);

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}
}