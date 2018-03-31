<?php
namespace Track\Domain\Service;

use Track\Model\Dto\TrackDto;
use Review\Model\Dto\ReviewMusicDto;
use Album\Model\Dto\AlbumDto;
use model\dto\CurlDto;
use util\Api;
use Fuel\Core\Uri;
final class TrackService
{
	public static function validation_for_detail($track_id)
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = \Validation::forge();

		$v = $obj_validation->add('track_id', 'トラックID');
		$v->add_rule('required');
		$v->add_rule('max_length', '19');
		$v->add_rule('valid_string', array('numeric'));

		$arr_params = array(
			'track_id' => $track_id,
		);

		if ( ! $obj_validation->run($arr_params))
		{
			foreach ($obj_validation->error() as $i => $val)
			{
				throw new \Exception($val->get_message());
			}
		}

		return true;
	}


	public static function set_dto_for_detail($track_id)
	{
		\Log::debug('[start]'. __METHOD__);

		$track_dto = TrackDto::get_instance();
		$track_dto->set_track_id(trim($track_id));

		$review_dto = ReviewMusicDto::get_instance();
		$review_dto->set_about('track');
		$review_dto->set_about_id(trim($track_id));
		$review_dto->set_page(Uri::segment(4, 1));
		$review_dto->set_limit(10);

		return true;
	}


	public static function set_request_to_dto()
	{
		\Log::debug('[start]'. __METHOD__);

		$album_dto        = AlbumDto::get_instance();
		$review_music_dto = ReviewMusicDto::get_instance();
		foreach (\Input::param() as $key => $val)
		{
			$method_name = 'set_'. $key;
			if (method_exists($review_music_dto, $method_name))
			{
				$review_music_dto->$method($val);
			}
			if (method_exists($album_dto, $method_name))
			{
				$album_dto->$method($val);
			}
		}
	}


	public static function get_track_info_only_tmp_review()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();
		$about = $review_dto->get_about();
;
		if ($about === 'track')
		{
			return static::get_track_info();
		}
		return false;
	}


	public static function get_track_info()
	{
		\Log::debug('[start]'. __METHOD__);

		$track_dto = TrackDto::get_instance();
		$album_dto = AlbumDto::get_instance();
		$arr_send = array();
		$arr_send['track_id'] = $track_dto->get_track_id();

		if (empty($arr_send['track_id']))
		{
			\Log::debug('track_idがセットされていません');
			return true;
		}

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/track/info.json');
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

		$obj_result = $obj_response->result;

		if (empty($obj_result))
		{
			throw new \Exception('apiレスポンスが空です');
		}

		foreach (get_object_public_vars($obj_result) as $prop => $val)
		{
			$method = 'set_'.$prop;
			if (method_exists($track_dto, $method))
			{
				$track_dto->$method($val);
			}
			if (method_exists($album_dto, $method))
			{
				$album_dto->$method($val);
			}
		}

		return true;
	}
}
