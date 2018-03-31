<?php
namespace Album\Domain\Service;

use Album\Model\Dto\AlbumDto;
use Review\Model\Dto\ReviewMusicDto;
use Artist\Model\Dto\ArtistDto;
use model\dto\CurlDto;
use util\Api;
use Fuel\Core\Validation;
use login\model\dto\LoginDto;
use Fuel\Core\Uri;

final class AlbumService
{
	public static function validation_for_detail($album_id)
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();

		$v = $obj_validation->add('album_id', 'アルバムID');
		$v->add_rule('required');
		$v->add_rule('max_length', 19);
		$v->add_rule('valid_string', array('numeric'));

		$arr_params = array(
			'album_id' => $album_id,
		);

		if ( ! $obj_validation->run($arr_params))
		{
			foreach ($obj_validation->error() as $i => $val)
			{
				throw new \Exception($val->get_message());
			}
		}
	}

	public static function set_dto_for_detail($album_id)
	{
		\Log::debug('[start]'. __METHOD__);

		$album_dto        = AlbumDto::get_instance();
		$album_dto->set_album_id(trim($album_id));

		$review_music_dto = ReviewMusicDto::get_instance();
		$review_music_dto->set_about('album');
		$review_music_dto->set_about_id(trim($album_id));
		$review_music_dto->set_page(Uri::segment(4, 1));
		$review_music_dto->set_limit(10);

		return true;
	}


	public static function get_album_info_only_tmp_review()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();
		$about = $review_dto->get_about();
		if ($about === 'album')
		{
			return static::get_album_info();
		}
		return false;
	}


	public static function get_album_info()
	{
		\Log::debug('[start]'. __METHOD__);

		$album_dto = AlbumDto::get_instance();
		$artist_dto = ArtistDto::get_instance();
		$arr_send = array();
		$arr_send['album_id'] = $album_dto->get_album_id();
		if (empty($arr_send['album_id']))
		{
			return true;
		}

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/album/info.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		$obj_result = $obj_response->result;

		if ( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiからのレスポンスが不正です');
		}

		$artist_dto->set_artist_id($obj_result->artist_id);

		$album_dto->set_artist_id($obj_result->artist_id);
		$album_dto->set_album_id($obj_result->album_id);
		$album_dto->set_album_name($obj_result->album_name);
		$album_dto->set_kana($obj_result->kana);
		$album_dto->set_english($obj_result->english);
		$album_dto->set_same_names($obj_result->same_names);
		$album_dto->set_album_mbid_itunes($obj_result->mbid_itunes);
		$album_dto->set_album_mbid_lastfm($obj_result->mbid_lastfm);
		$album_dto->set_album_url_itunes($obj_result->url_itunes);
		$album_dto->set_album_url_lastfm($obj_result->url_lastfm);
		$album_dto->set_image_url($obj_result->image_url);
		$album_dto->set_image_small($obj_result->image_small);
		$album_dto->set_image_medium($obj_result->image_medium);
		$album_dto->set_image_large($obj_result->image_large);
		$album_dto->set_image_extralarge($obj_result->image_extralarge);
		$album_dto->set_release_itunes($obj_result->release_itunes);
		$album_dto->set_copyright_itunes($obj_result->copyright_itunes);
		$album_dto->set_genre_itunes($obj_result->genre_itunes);
		$album_dto->set_arr_list($obj_result->arr_list);

		return true;
	}

	/**
	 * アーティストIDからGOLデータベースからアルバム情報を取得
	 *
	 * @throws \Exception
	 * @return boolean
	 */
	public static function get_album_list()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$album_dto  = AlbumDto::get_instance();
		$login_dto  = LoginDto::get_instance();

		$arr_send = array();
		$arr_send['artist_id']   = $artist_dto->get_artist_id();
		$limit                   = $album_dto->get_limit();
		$page                    = $album_dto->get_page();
		$arr_send['limit']       = empty($limit)? 20: $limit;
		$arr_send['page']        = empty($page)? 1: $page;

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/album/list/true.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		if ( ! property_exists($obj_response, 'result'))
		{
			return true;
		}

		$arr_gol_result = array();
		if (property_exists($obj_response->result, 'arr_list'))
		{
			foreach ($obj_response->result->arr_list as $i => $obj_gol)
			{
				$name = $obj_gol->name;
				$mbid_itunes = $obj_gol->mbid_itunes;
				$mbid_lastfm = $obj_gol->mbid_lastfm;
				$mbid = empty($mbid_itunes)? $mbid_lastfm: $mbid_itunes;
				$mbid_for_key = empty($mbid) ? 'mbid_'. $i : $mbid;
				$mbid_for_key .= '_'. $name;
				$arr_gol_result[$mbid_for_key] = array(
					'id'               => $obj_gol->id,
					'name'             => $obj_gol->name,
					'kana'             => $obj_gol->kana,
					'english'          => $obj_gol->english,
					'same_names'       => $obj_gol->same_names,
					'mbid_itunes'      => $obj_gol->mbid_itunes,
					'mbid_lastfm'      => $obj_gol->mbid_lastfm,
					'url_itunes'       => $obj_gol->url_itunes,
					'url_lastfm'       => $obj_gol->url_lastfm,
					'image_url'        => $obj_gol->image_url,
					'image_small'      => $obj_gol->image_small,
					'image_medium'     => $obj_gol->image_medium,
					'image_large'      => $obj_gol->image_large,
					'image_extralarge' => $obj_gol->image_extralarge,
				);
			}
		}

		$album_dto->set_arr_list($arr_gol_result);

		return true;
	}
}
