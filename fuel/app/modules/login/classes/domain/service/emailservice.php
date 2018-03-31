<?php
namespace login\domain\service;

use login\model\dto\LoginDto;
use user\model\dto\UserDto;
final class EmailService
{
	/**
	 * 仮パスワードを発行しメールで送信
	 * @return boolean
	 */
	public static function send_reissue_password()
	{
		\Log::debug('[start]'. __METHOD__);

		try
		{
			$user_dto  = UserDto::get_instance();
			$login_dto = LoginDto::get_instance();

			$from_email = \Config::get('define.email.address.info');
			$from_name  = \Config::get('define.email.name.info');
			$to_email = $user_dto->get_email();

			$arr_params = array(
					'email'              => $to_email,
					'tentative_id'       => $user_dto->get_user_id(),
					'tentative_password' => $user_dto->get_password(),
			);

			$body  = 'グルーヴオンラインパスワードの再登録を受け付けました。'. PHP_EOL;
			$body .= 'パスワード再登録は以下のリンク先で'. $login_dto->get_passreissue_expired_min().'分以内におこなってください。'. PHP_EOL;
			$body .= \Config::get('host.base_url_https'). '/login/grooveonlinepassreissueform/'. $user_dto->get_user_id(). '/?'. http_build_query($arr_params);
			$body .= PHP_EOL. PHP_EOL. PHP_EOL;
			$body .= "team グルーヴオンライン プロジェクト". PHP_EOL;
			$body .= "http://groove-online.com/";

			$enc_body = mb_convert_encoding($body, 'jis');

			$obj_email = \Email::forge('jis');
			$obj_email->from($from_email, $from_name);
			$obj_email->to($to_email);
			$obj_email->priority(\Email::P_HIGH);
			$obj_email->subject('グルーヴオンラインパスワード再登録');
			$obj_email->body($enc_body);
			$obj_email->send();

			return true;
		}
		catch(\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			$env = isset($_SERVER['FUEL_ENV']) ? $_SERVER['FUEL_ENV'] : null;
			if ($env === \Fuel::PRODUCTION)
			{
				throw new \Exception($e);
			}

			\Log::error('stagingでのメール送信は行いません');
			return true;
		}
	}

	/**
	 * パスワード更新が完了したことをユーザにメールで報告
	 * @return boolean
	 */
	public static function send_reissue_password_done()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$from_email = \Config::get('define.email.address.info');
		$from_name  = \Config::get('define.email.name.info');
		$to_email = \Input::post('email');

		$body = 'グルーヴオンラインのログインパスワードの変更を受け付けました。';
		$enc_body = mb_convert_encoding($body, 'jis');

		$obj_email = \Email::forge('jis');
		$obj_email->from($from_email, $from_name);
		$obj_email->to($to_email);
		$obj_email->priority(\Email::P_HIGH);
		$obj_email->subject('グルーヴオンラインパスワード再登録完了');
		$obj_email->body($enc_body);
		$obj_email->send();

		return true;
	}
}