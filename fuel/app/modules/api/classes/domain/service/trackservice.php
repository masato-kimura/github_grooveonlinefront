<?php
namespace Api\domain\service;

use Api\Model\dto\TrackDto;
use Api\Model\dto\AlbumDto;
use Artist\Model\Dto\ArtistDto;
use util\Api;
use model\dto\CurlDto;
use model\domain\service\Service;
use Fuel\Core\Validation;

final class TrackService extends Service
{
	private static $_obj_response;
	private static $arr_result = array();


	public static function validation_for_albumtracklist()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデートで使用するため obj_requestの値を$_POSTにセットする
		static::_set_request_to_post(static::$_obj_request);

		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('album_id', 'アルバムID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		# バリデート実行
		static::_validate_run($obj_validate);

		return true;
	}


	public static function validation_for_info()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデートで使用するため obj_requestの値を$_POSTにセットする
		static::_set_request_to_post(static::$_obj_request);

		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('track_id', 'トラックID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

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

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('artist_id', 'アーティストID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('track_name', 'トラック検索名');
		$v->add_rule('required');
		$v->add_rule('max_length', '100');

		# バリデート実行
		static::_validate_run($obj_validate);

		return true;
	}


	public static function set_dto_for_albumtracklist()
	{
		\Log::debug('[start]'. __METHOD__);

		$album_dto = AlbumDto::get_instance();
		$album_dto->set_album_id(trim(static::$_obj_request->album_id));

		return true;
	}


	public static function set_dto_for_info()
	{
		\Log::debug('[start]'. __METHOD__);

		$track_dto = TrackDto::get_instance();
		$track_dto->set_track_id(trim(static::$_obj_request->track_id));

		return true;
	}


	public static function set_dto_for_search()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$track_dto  = TrackDto::get_instance();

		$artist_dto->set_artist_id(trim(static::$_obj_request->artist_id));
		$track_dto->set_track_name(trim(static::$_obj_request->track_name));

		return true;
	}


	/**
	 * アーティストIDまたはアルバム名からGOLデータベースからアルバム情報を取得
	 * アルバムトラックスを取得
	 * @throws \Exception
	 * @return boolean
	 */
	public static function get_list()
	{
		\Log::debug('[start]'. __METHOD__);

		$track_dto = TrackDto::get_instance();
		$album_dto = AlbumDto::get_instance();

		$arr_send = array();
		$arr_send['album_id']   = $album_dto->get_album_id();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/track/albumtracklist.json');
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

		foreach ($obj_response->result->arr_list as $i => $obj_gol)
		{
			static::$arr_result[] = array(
					'id'               => $obj_gol->id,
					'artist_id'        => $obj_gol->artist_id,
					'album_id'         => $obj_gol->album_id,
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
					'duration'         => $obj_gol->duration,
					'genre_itunes'     => $obj_gol->genre_itunes,
					'preview_itunes'   => $obj_gol->preview_itunes,
					'number'           => $obj_gol->number,
					'content'          => $obj_gol->content,
			);
		}

		$track_dto->set_arr_list(static::$arr_result);
		$album_dto->set_release_itunes($obj_response->result->release_itunes);
		$album_dto->set_copyright_itunes($obj_response->result->copyright_itunes);
		$album_dto->set_genre_itunes($obj_response->result->genre_itunes);

		return true;
	}


	/**
	 * アーティストIDまたはアルバム名からGOLデータベースからトラック情報を取得
	 *
	 * @throws \Exception
	 * @return boolean
	 */
	public static function get_info()
	{
		\Log::debug('[start]'. __METHOD__);

		$track_dto = TrackDto::get_instance();

		$arr_send = array();
		$arr_send['track_id'] = $track_dto->get_track_id();

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
			throw new \Exception('該当のトラック情報が取得できません empty($result)');
		}

		$arr_result = array();
		$image_small             = $obj_response->result->image_small;
		$image_small             = empty($image_small)? \Config::get('image.default.track.small'): $image_small;
		$image_medium            = $obj_response->result->image_medium;
		$image_medium            = empty($image_medium)? \Config::get('image.default.track.medium'): $image_medium;
		$image_large             = $obj_response->result->image_large;
		$image_large             = empty($image_large)? \Config::get('image.default.track.large'): $image_large;
		$image_extralarge        = $obj_response->result->image_extralarge;
		$image_extralarge        = empty($image_extralarge)? \Config::get('image.default.track.extralarge'): $image_extralarge;
		$artist_image_small      = $obj_response->result->artist_image_small;
		$artist_image_small      = empty($artist_image_small)?      \Config::get('image.default.artist.small'): $artist_image_small;
		$artist_image_medium     = $obj_response->result->artist_image_medium;
		$artist_image_medium     = empty($artist_image_medium)? \Config::get('image.default.artist.medium'): $artist_image_medium;
		$artist_image_large      = $obj_response->result->artist_image_large;
		$artist_image_large      = empty($artist_image_large)? \Config::get('image.default.artist.large'): $artist_image_large;
		$artist_image_extralarge = $obj_response->result->artist_image_extralarge;
		$artist_image_extralarge = empty($artist_image_extralarge)? \Config::get('image.default.artist.extralarge'): $artist_image_extralarge;

		$arr_result[] = array(
			'id'                      => $obj_response->result->id,
			'name'                    => $obj_response->result->name,
			'kana'                    => $obj_response->result->kana,
			'english'                 => $obj_response->result->english,
			'mbid_itunes'             => $obj_response->result->mbid_itunes,
			'mbid_lastfm'             => $obj_response->result->mbid_lastfm,
			'url_itunes'              => $obj_response->result->url_itunes,
			'url_lastfm'              => $obj_response->result->url_lastfm,
			'image_small'             => $image_small,
			'image_medium'            => $image_medium,
			'image_large'             => $image_large,
			'image_extralarge'        => $image_extralarge,
			'release_itunes'          => $obj_response->result->release_itunes,
			'release_lastfm'          => $obj_response->result->release_lastfm,
			'genre_itunes'            => $obj_response->result->genre_itunes,
			'duration'                => $obj_response->result->duration,
			'preview_itunes'          => $obj_response->result->preview_itunes,
			'number'                  => $obj_response->result->number,
			'content'                 => $obj_response->result->content,
			'artist_id'               => $obj_response->result->artist_id,
			'artist_name'             => $obj_response->result->artist_name,
			'artist_image_small'      => $artist_image_small,
			'artist_image_medium'     => $artist_image_medium,
			'artist_image_large'      => $artist_image_large,
			'artist_image_extralarge' => $artist_image_extralarge,
			'album_id'                => $obj_response->result->album_id,
			'album_name'              => $obj_response->result->album_name,
		);

		$track_dto->set_arr_list($arr_result);

		return true;
	}


	public static function search()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$track_dto  = TrackDto::get_instance();

		$arr_send = array();
		$arr_send['artist_id']         = $artist_dto->get_artist_id();
		$arr_send['track_name']        = $track_dto->get_track_name();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/track/search.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
\Log::info($obj_response);
		if ( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiレスポンスが不正です not exist property result');
		}
		if ( ! property_exists($obj_response->result, 'arr_list'))
		{
			throw new \Exception('apiレスポンスが不正です not exist property arr_list');
		}

		foreach ($obj_response->result->arr_list as $i => $obj_gol)
		{
			$image_url        = $obj_gol->image_url;
			$image_url        = empty($image_url)? \Config::get('image.default.track.origin'): $image_url;
			$image_small      = $obj_gol->image_small;
			$image_small      = empty($image_small) ? \Config::get('image.default.track.small'): $image_small;
			$image_medium     = $obj_gol->image_medium;
			$image_medium     = empty($image_medium)? \Config::get('image.default.track.medium'): $image_medium;
			$image_large      = $obj_gol->image_large;
			$image_large      = empty($image_large)? \Config::get('image.default.track.large'): $image_large;
			$image_extralarge = $obj_gol->image_extralarge;
			$image_extralarge = empty($image_extralarge)? \Config::get('image.default.track.extralarge'): $image_extralarge;

			static::$arr_result[] = array(
				'id'               => $obj_gol->id,
				'name'             => $obj_gol->name,
				'kana'             => $obj_gol->kana,
				'english'          => $obj_gol->english,
				'same_names'       => $obj_gol->same_names,
				'mbid_itunes'      => $obj_gol->mbid_itunes,
				'mbid_lastfm'      => $obj_gol->mbid_lastfm,
				'url_itunes'       => $obj_gol->url_itunes,
				'url_lastfm'       => $obj_gol->url_lastfm,
				'image_url'        => $image_url,
				'image_small'      => $image_small,
				'image_medium'     => $image_medium,
				'image_large'      => $image_large,
				'image_extralarge' => $image_extralarge,
				'release_itunes'   => $obj_gol->release_itunes,
				'genre_itunes'     => $obj_gol->genre_itunes,
				'duration'         => $obj_gol->duration,
				'preview_itunes'   => $obj_gol->preview_itunes,
				'content'          => $obj_gol->content,
				'artist_id'        => $obj_gol->artist_id,
				'album_id'         => $obj_gol->album_id,
			);
		}

		$track_dto->set_arr_list(static::$arr_result);

		return true;
	}


	private static function _is_english($name)
	{
		if (preg_match('/^[a-z0-9\s\s\&\[\]\'\-:\/]*$/i', $name))
		{
			return true;
		}
		return false;
	}
}
