<?php
namespace Api;

use Api\domain\service\ApiLoginService;
final class Controller_Login extends \Controller_Rest
{
	/**
	 * emailとpasswordでログインできるか
	 * @throws \Exception
	 * @return boolean
	 */
	public function post_grooveonlineavailablelogin()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			ApiLoginService::get_json_request();

			# バリデーションチェック
			ApiLoginService::validation_for_is_available_login();

			# DTOにリクエストをセット
			ApiLoginService::set_dto_for_is_available_login();

			# APIに問い合わせ
			if (ApiLoginService::is_available_login())
			{
				# セッションに格納
				\Session::set('available_login', true);

				# APIレスポンス
				$arr_response = array(
				'success'  => true,
				'code'     => 1001,
				'response' => 'ログイン有効',
				'result'   => array(
						'is_available' => true,
					),
				);
			}
			else
			{
				# セッションを削除
				\Session::delete('available_login');

				# APIレスポンス
				$arr_response = array(
				'success'  => true,
				'code'     => 7010,
				'response' => 'ログインできません',
				'result'   => array(
						'is_available' => false,
					),
				);
			}

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);

			return true;
		}
		catch (\Exception $e)
		{
			$arr_response = array('result' => null, 'success' => false, 'code' => $e->getCode(), 'response' => $e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');
			\Log::error($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}
}
