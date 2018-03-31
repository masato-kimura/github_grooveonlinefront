<?php
namespace Api\domain\service;

use Api\Model\dto\AlbumDto;
use Artist\Model\Dto\ArtistDto;
use util\Api;
use model\dto\CurlDto;
use model\domain\service\Service;
use Fuel\Core\Validation;

final class AlbumService extends Service
{
	private static $_obj_response;
	private static $arr_gol_result    = array();
	private static $album_list_limit = 48;


	public static function validation_for_list()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデートで使用するため obj_requestの値を$_POSTにセットする
		static::_set_request_to_post(static::$_obj_request);

		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('artist_id', 'アーティストID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('page', 'ページ');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', '1');
		$v->add_rule('numeric_max', '100000000');

		$v = $obj_validate->add('limit', '１ページあたりのアルバム表示数');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', '1');
		$v->add_rule('numeric_max', '200');

		# バリデート実行
		static::_validate_run($obj_validate);

		return true;
	}


	public static function validation_for_search()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデートで使用するため obj_requestの値を$_POSTにセットする
		static::_set_request_to_post(static::$_obj_request);

		$obj_validate = Validation::forge();

		/* API共通バリデート設定 */
		static::_validate_base($obj_validate);

		/* 個別バリデート設定 */
		# artist_id
		$v = $obj_validate->add('artist_id', 'アーティストID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		# album_name
		$v = $obj_validate->add('album_name', 'アルバム検索名');
		$v->add_rule('required');
		$v->add_rule('max_length', '100');

		# バリデート実行
		static::_validate_run($obj_validate);

		return true;
	}


	public static function set_dto_for_list()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$album_dto  = AlbumDto::get_instance();

		foreach (static::$_obj_request as $key => $val)
		{
			if ( ! isset($val))
			{
				continue;
			}
			if ($key === 'artist_id')
			{
				$artist_dto->set_artist_id(trim($val));
				$album_dto->set_artist_id(trim($val));
			}
			if ($key === 'page')
			{
				$page = trim($val);
				$page = empty($page)? 1: $page;
				$album_dto->set_page($page);
			}
			if ($key === 'limit')
			{
				$limit = trim($val);
				if (empty($limit) or ($limit > static::$album_list_limit))
				{
					$limit = static::$album_list_limit;
				}
				$album_dto->set_limit($limit);
			}
		}

		return true;
	}


	public static function set_dto_for_search()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$album_dto  = AlbumDto::get_instance();

		foreach (static::$_obj_request as $key => $val)
		{
			if ( ! isset($val))
			{
				continue;
			}
			if ($key === 'artist_id')
			{
				$artist_dto->set_artist_id(trim($val));
				$album_dto->set_artist_id(trim($val));
			}
			if ($key === 'album_name')
			{
				$album_dto->set_album_name(trim($val));
			}
		}

		return true;
	}

	/**
	 * アーティストIDからアルバムリストを取得
	 *
	 * @throws \Exception
	 * @return boolean
	 */
	public static function get_list()
	{
		\Log::debug('[start]'. __METHOD__);

		$album_dto = AlbumDto::get_instance();

		$arr_send = array();
		$arr_send['artist_id']   = $album_dto->get_artist_id();
		$arr_send['limit']       = $album_dto->get_limit();
		$page                    = $album_dto->get_page();
		$arr_send['page']        = empty($page)? 1: $page;

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/album/list.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();

		if (property_exists($obj_response->result, 'arr_list'))
		{
			foreach ($obj_response->result->arr_list as $i => $obj_gol)
			{
				static::$arr_gol_result[] = array(
					'id'               => $obj_gol->id,
					'name'             => $obj_gol->name,
					'kana'             => $obj_gol->kana,
					'english'          => $obj_gol->english,
					'mbid_itunes'      => $obj_gol->mbid_itunes,
					'mbid_lastfm'      => $obj_gol->mbid_lastfm,
					'url_itunes'       => $obj_gol->url_itunes,
					'url_lastfm'       => $obj_gol->url_lastfm,
					'image_url'        => $obj_gol->image_url,
					'image_small'      => $obj_gol->image_small,
					'image_medium'     => $obj_gol->image_medium,
					'image_large'      => $obj_gol->image_large,
					'image_extralarge' => $obj_gol->image_extralarge,
					'sort'             => $obj_gol->sort,
					'release_itunes'   => $obj_gol->release_itunes,
					'copyright_itunes' => $obj_gol->copyright_itunes,
					'genre_itunes'     => $obj_gol->genre_itunes,
				);
			}
		}

		return true;
	}


	/**
	 * アーティストIDとアルバム名でアルバム検索
	 *
	 * @throws \Exception
	 * @return boolean
	 */
	public static function search_list()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$album_dto  = AlbumDto::get_instance();

		$arr_send = array();
		$arr_send['artist_id']   = $album_dto->get_artist_id();
		$arr_send['album_name']  = $album_dto->get_album_name();
		$arr_send['limit']       = $album_dto->get_limit();
		$page                    = $album_dto->get_page();
		$arr_send['page']        = empty($page)? 1: $page;

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/album/search.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();

		if (property_exists($obj_response->result, 'arr_list'))
		{
			foreach ($obj_response->result->arr_list as $i => $obj_gol)
			{
				$name = $obj_gol->name;
				static::$arr_gol_result[] = array(
					'id'               => $obj_gol->id,
					'name'             => $obj_gol->name,
					'kana'             => $obj_gol->kana,
					'english'          => $obj_gol->english,
					'mbid_itunes'      => $obj_gol->mbid_itunes,
					'mbid_lastfm'      => $obj_gol->mbid_lastfm,
					'url_itunes'       => $obj_gol->url_itunes,
					'url_lastfm'       => $obj_gol->url_lastfm,
					'image_url'        => $obj_gol->image_url,
					'image_small'      => $obj_gol->image_small,
					'image_medium'     => $obj_gol->image_medium,
					'image_large'      => $obj_gol->image_large,
					'image_extralarge' => $obj_gol->image_extralarge,
					'release_itunes'   => $obj_gol->release_itunes,
					'copyright_itunes' => $obj_gol->copyright_itunes,
					'genre_itunes'     => $obj_gol->genre_itunes,
					'sort'             => $obj_gol->sort,
				);
			}
		}

		return true;
	}


	public static function format_for_dto()
	{
		\Log::debug('[start]'. __METHOD__);

		$album_api_dto = AlbumDto::get_instance();
		$arr_result = static::$arr_gol_result;

		if (empty($arr_result))
		{
			$album_api_dto->set_arr_list(array());

			return true;
		}

		foreach ($arr_result as $i => $val)
		{
			$image_url = $val['image_url'];
			if (empty($image_url))
			{
				$default_image_url = \Config::get('host.img_local_url'). 'profile/user/default/default.jpg';
				$arr_result[$i]['image_url'] = $default_image_url;
			}
		}

		$album_api_dto->set_arr_list($arr_result);

		return true;
	}
}
