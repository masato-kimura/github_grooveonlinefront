<?php
namespace Api;

use Api\domain\service\ApiFavoriteService;
final class Controller_Favorite extends \Controller_Rest
{
	private $count_favorite;


	/**
	 * お気に入りユーザに登録
	 * @throws \Exception
	 * @return boolean
	 */
	public function post_set()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			ApiFavoriteService::get_json_request();

			# バリデーションチェック
			ApiFavoriteService::validation_for_set();

			# DTOにリクエストをセット
			ApiFavoriteService::set_dto_for_set();

			# セッションに登録
			ApiFavoriteService::set_session();

			# APIに送信
			ApiFavoriteService::send_api();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => 1001,
				'response' => 'set favorite done',
				'result'   => array(
						'done' => true,
					),
			);

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
