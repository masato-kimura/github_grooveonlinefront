<?php
namespace Rank\domain\service;

use rank\model\dto\RankDto;
use model\dto\CurlDto;
use util\Api;
final class RankService
{
	public static function get_rank_weekly()
	{
		try
		{
			\Log::debug('[start]'. __METHOD__);

			$rank_dto = RankDto::get_instance();
			$arr_send = array(
				'offset' => $rank_dto->get_offset(),
				'limit'  => $rank_dto->get_limit(),
			);

			# CURL送信のための情報をDTOにセット
			$curl_dto = CurlDto::get_instance();
			switch ($rank_dto->get_about())
			{
				case 'track':
					$url = \Config::get('host.api_url'). 'main/rank/week/track.json';
					break;
				case 'album':
					$url = \Config::get('host.api_url'). 'main/rank/week/album.json';
					break;
				default:
					$url = \Config::get('host.api_url'). 'main/rank/week/track.json';
			}
			$curl_dto->set_url($url);
			$curl_dto->set_arr_send($arr_send);

			# CURLにてAPI送信
			$obj_api = new Api();
			$obj_api->send_curl();

			# CURL送信レスポンス
			$obj_response = $obj_api->get_curl_response();
			if ( ! property_exists($obj_response, 'result'))
			{
				throw new \Exception('apiレスポンスが不正です not exist property result');
			}
			if ( ! property_exists($obj_response->result, 'arr_list'))
			{
				throw new \Exception('apiレスポンスが不正です not exist property arr_list');
			}

			$rank_dto->set_arr_list($obj_response->result->arr_list);

			return true;

		}
		catch (\Exception $e)
		{
			throw new \Exception($e);
		}
	}
}