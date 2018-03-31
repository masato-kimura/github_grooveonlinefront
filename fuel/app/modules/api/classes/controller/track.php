<?php
namespace Api;

use Api\domain\service\TrackService;
use Api\Model\dto\TrackDto;
use Album\Model\Dto\AlbumDto;

final class Controller_Track extends \Controller_Rest
{
	/**
	 * アルバムIDから収録トラックを取得
	 * @return boolean
	 */
	public function post_albumtracklist()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			TrackService::get_json_request();

			# バリデーションチェック
			TrackService::validation_for_albumtracklist();

			# DTOにリクエストをセット
			TrackService::set_dto_for_albumtracklist();

			# APIからリスト取得
			TrackService::get_list();

			$album_dto = \Api\Model\dto\AlbumDto::get_instance();
			$track_dto = TrackDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'get track info',
				'result'   => array(
					'arr_list' => $track_dto->get_arr_list(),
					'release_itunes' => $album_dto->get_release_itunes(),
					'copyright_itunes' => $album_dto->get_copyright_itunes(),
					'genre_itunes' => $album_dto->get_genre_itunes(),
				),
			);

			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
			$this->response($arr_response);
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			$arr_response = array(
				'success'  => false,
				'code'     => $e->getCode(),
				'response' => $e->getMessage(),
				'result'   => null,
			);

			$this->response($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}


	/**
	 * トラックIDからトラック詳細情報を取得
	 * @return boolean
	 */
	public function post_info()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			TrackService::get_json_request();

			# バリデーションチェック
			TrackService::validation_for_info();

			# DTOにリクエストをセット
			TrackService::set_dto_for_info();

			# APIからリスト取得
			TrackService::get_info();

			$track_dto = TrackDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'get track one_info',
				'result'   => $track_dto->get_arr_list(),
			);

			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
			$this->response($arr_response);
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			$arr_response = array(
				'success'  => false,
				'code'     => $e->getCode(),
				'response' => $e->getMessage(),
				'result'   => null,
			);

			$this->response($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}


	/**
	 * トラック検索
	 * (検索リストに表示する用)
	 *
	 */
	public function post_search()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			TrackService::get_json_request();

			# バリデーションチェック
			TrackService::validation_for_search();

			# DTOにリクエストをセット
			TrackService::set_dto_for_search();

			# APIからリスト取得
			TrackService::search();

			$track_dto = TrackDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'get track one_info',
				'result'   => $track_dto->get_arr_list(),
			);

			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
			$this->response($arr_response);
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			$arr_response = array(
				'success'  => false,
				'code'     => $e->getCode(),
				'response' => $e->getMessage(),
				'result'   => null,
			);

			$this->response($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}
}
