<?php
namespace Api;

use Api\domain\service\ArtistService;
use Artist\Model\Dto\ArtistDto;

final class Controller_Artist extends \Controller_Rest
{
	public function post_search()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			ArtistService::get_json_request();

			# バリデーションチェック
			ArtistService::validation_for_search();

			# DTOにリクエストをセット
			ArtistService::set_dto_for_search();

			# APIからリスト取得
			ArtistService::get_search();

			# フォーマット
			ArtistService::format_for_dto();

			$artist_dto = ArtistDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'get artist list',
				'result'   => array(
					'arr_list' => $artist_dto->get_arr_list(),
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
		}
		catch (\Exception $e)
		{
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
	 * 過去に検索されたアーティスト一覧を取得
	 */
	public function post_getlastsearchlist()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			ArtistService::get_json_request();

			# バリデーションチェック
			ArtistService::validation_for_getlastsearchlist();

			# DTOにリクエストをセット
			ArtistService::set_dto_for_getlastsearchlist();

			# APIからリスト取得
			ArtistService::get_lastsearchlist();

			$artist_dto = ArtistDto::get_instance();

			# APIレスポンス
			$arr_response = array(
			'success'  => true,
			'code'     => '1001',
			'response' => 'get last search list',
			'result'   => array(
				'arr_list' => $artist_dto->get_arr_list(),
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
		}
		catch (\Exception $e)
		{
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
	 * お気に入りアーティストに登録
	 * @throws \Exception
	 * @return boolean
	 */
	public function post_setfavorite()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			ArtistService::get_json_request();

			# バリデーションチェック
			ArtistService::validation_for_setfavorite();

			# DTOにリクエストをセット
			ArtistService::set_dto_for_setfavorite();

			# セッションに登録
			ArtistService::set_session();

			# APIに送信
			ArtistService::send_api();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => 1001,
				'response' => 'set favorite artist done',
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
