<?php
namespace Api;

use Api\domain\service\AlbumService;
use Api\Model\dto\AlbumDto;

final class Controller_Album extends \Controller_Rest
{
	public function post_list()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			AlbumService::get_json_request();

			# バリデーションチェック
			AlbumService::validation_for_list();

			# DTOにリクエストをセット
			AlbumService::set_dto_for_list();

			# APIからリスト取得
			AlbumService::get_list();

			# レスポンスフォーマット
			AlbumService::format_for_dto();

			$album_dto = AlbumDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'get artist info',
				'result'   => array(
					'arr_list' => $album_dto->get_arr_list(),
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			$arr_response = array(
				'success' => false,
				'code' => '7001',
				'response' => $e->getMessage(),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
		}
	}


	/**
	 * アルバム名を検索ワードにアルバムを検索する
	 */
	public function post_search()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			AlbumService::get_json_request();

			# バリデーションチェック
			AlbumService::validation_for_search();

			# DTOにリクエストをセット
			AlbumService::set_dto_for_search();

			# APIからリスト取得
			AlbumService::search_list();

			# レスポンスフォーマット
			AlbumService::format_for_dto();

			$album_dto = AlbumDto::get_instance();

			# APIレスポンス
			$arr_response = array(
			'success'  => true,
			'code'     => '1001',
			'response' => 'get artist info',
			'result'   => array(
					'arr_list' => $album_dto->get_arr_list(),
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			$arr_response = array(
				'success' => false,
				'code' => '7001',
				'response' => 'busy lastfm'
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
		}
	}

}
