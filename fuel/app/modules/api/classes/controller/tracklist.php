<?php
namespace Api;

use Tracklist\Domain\Service\TracklistService;
use Tracklist\Model\Dto\TracklistDto;
use login\domain\service\LoginService;
use user\model\dto\UserDto;
final class Controller_Tracklist extends \Controller_Rest
{
	/**
	 * リスト投稿する
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
			TracklistService::get_json_request();

			# ログイン情報を取得
			LoginService::set_user_info_to_dto_from_session();

			# バリデーションチェック
			TracklistService::validation_for_set();

			# DTOにリクエストをセット
			TracklistService::set_dto_for_set();

			# APIへリストを送信
			TracklistService::send_list_to_api();

			$tracklist_dto = TracklistDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'send_list_done',
				'result'   => array(
					'tracklist_id'   => $tracklist_dto->get_tracklist_id(),
				),
			);
			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
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


	/**
	 * リストを削除
	 * @throws \Exception
	 * @return boolean
	 */
	public function post_delete()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			TracklistService::get_json_request();

			# ログイン情報を取得
			LoginService::set_user_info_to_dto_from_session();

			# バリデーションチェック
			TracklistService::validation_for_delete();

			# DTOにリクエストをセット
			TracklistService::set_dto_for_delete();

			# APIへリストを送信
			TracklistService::delete_from_api();

			$tracklist_dto = TracklistDto::get_instance();

			# APIレスポンス
			$arr_response = array(
			'success'  => true,
			'code'     => '1001',
			'response' => 'delete_done',
			'result'   => array(
				'tracklist_id'   => $tracklist_dto->get_tracklist_id(),
				),
			);
			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
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


	/**
	 * リスト取得
	 * @throws \Exception
	 * @return boolean
	 */
	public function post_getlist()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			TracklistService::get_json_request();

			# バリデーションチェック
			TracklistService::validation_for_getlist();

			# DTOにリクエストをセット
			TracklistService::set_dto_for_getlist();

			# APIから取得
			TracklistService::get_list_from_api();

			$tracklist_dto = TracklistDto::get_instance();
			$user_dto = UserDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'get tracklist done',
				'result'   => array(
					'arr_list' => $tracklist_dto->get_arr_list(),
					'count'    => $tracklist_dto->get_count(),
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
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


	/**
	 * 詳細
	 * @throws \Exception
	 * @return boolean
	 */
	public function post_detail()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			TracklistService::get_json_request();

			# バリデーションチェック
			TracklistService::validation_for_detail();

			# DTOにリクエストをセット
			TracklistService::set_dto_for_detail();

			# APIから取得
			TracklistService::get_detail_from_api();

			$tracklist_dto = TracklistDto::get_instance();
			$user_dto = UserDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'get detail done',
				'result'   => array(
					'title'      => $tracklist_dto->get_title(),
					'user_id'    => $tracklist_dto->get_user_id(),
					'user_name'  => $tracklist_dto->get_user_name(),
					'created_at' => $tracklist_dto->get_created_at(),
					'arr_list'   => $tracklist_dto->get_arr_list(),
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
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
