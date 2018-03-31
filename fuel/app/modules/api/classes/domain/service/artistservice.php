<?php
namespace Api\domain\service;

use Artist\Model\Dto\ArtistDto;
use review\model\dto\ReviewMusicDto;
use model\dto\CurlDto;
use util\Api;
use model\domain\service\Service;
use Fuel\Core\Validation;
use login\domain\service\LoginService;
use login\model\dto\LoginDto;
use user\model\dto\UserDto;

final class ArtistService extends Service
{
	private static $_obj_response;
	private static $_arr_response = array();
	private static $_artist_search_limit = 10;
	private static $before_count_favorite;
	private static $update_count_favorite;


	/**
	 * バリデーション
	 * @throws \Exception
	 * @return boolean
	 */
	public static function validation_for_search()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデートで使用するため obj_requestの値を$_POSTにセットする
		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('artist_name', 'アーティスト検索名');
		$v->add_rule('required');
		$v->add_rule('max_length', '200');

		$v = $obj_validate->add('available_play', '再生可能フラグ');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', 0);
		$v->add_rule('numeric_max', 1);

		# バリデート実行
		$arr_params = array(
			'artist_name'    => static::$_obj_request->artist_name,
		);
		if (isset(static::$_obj_request->available_play))
		{
			$arr_params['available_play'] = static::$_obj_request->available_play;
		}
		static::_validate_run($obj_validate, $arr_params);

		return true;
	}


	public static function validation_for_getlastsearchlist()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();

		$v = $obj_validation->add('page');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', '1');
		$v->add_rule('numeric_max', '100');

		$arr_value = array(
			'page' => static::$_obj_request->page,
		);

		if ( ! $obj_validation->run($arr_value))
		{
			foreach ($obj_validation->error() as $e => $error)
			{
				throw new \Exception($error->get_message());
			}
		}

		return true;
	}


	public static function validation_for_setfavorite()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable(new self());

		$v = $obj_validation->add('client_user_id');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('unauthorized_check');
		$v->add_rule('max_length', '19');

		$v = $obj_validation->add('favorite_artist_id');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validation->add('status');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', 0);
		$v->add_rule('numeric_max', 1);

		$arr_value = array(
			'client_user_id'     => static::$_obj_request->client_user_id,
			'favorite_artist_id' => static::$_obj_request->favorite_artist_id,
			'status'             => static::$_obj_request->status,
		);

		if ( ! $obj_validation->run($arr_value))
		{
			foreach ($obj_validation->error() as $e => $error)
			{
				throw new \Exception($error->get_message());
			}
		}

		return true;
	}


	public static function set_dto_for_search()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();

		foreach (static::$_obj_request as $key => $val)
		{
			if ( ! isset($val))
			{
				continue;
			}
			if ($key === 'artist_name')
			{
				$artist_dto->set_artist_name(trim($val));
			}
			if ($key === 'page')
			{
				$artist_dto->set_page((int)($val));
			}
			if ($key === 'available_play')
			{
				$artist_dto->set_available_play($val);
			}
		}

		return true;
	}


	public static function set_dto_for_getlastsearchlist()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$artist_dto->set_page(trim(static::$_obj_request->page));

		return true;
	}


	public static function set_dto_for_setfavorite()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto  = LoginDto::get_instance();
		$user_dto   = UserDto::get_instance();
		$artist_dto = ArtistDto::get_instance();

		LoginService::set_user_info_to_dto_from_session();
		$user_id = $login_dto->get_user_id();

		if (isset($user_id))
		{
			$user_dto->set_user_id($user_id);
		}

		$artist_dto->set_artist_id(static::$_obj_request->favorite_artist_id);
		$artist_dto->set_favorite_status(static::$_obj_request->status);

		return true;
	}


	/**
	 * バリデーション
	 * @throws \Exception
	 * @return boolean
	 */
	public static function validation_for_review_write()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$review_dto = ReviewMusicDto::get_instance();

		$artist_id   = $review_dto->get_artist_id();
		$artist_name = $review_dto->get_artist_name();
		$user_id     = $review_dto->get_review_user_id();
		$is_delete   = $review_dto->get_is_delete();

		switch ($review_dto->get_about())
		{
			case 'artist':
				$review = $review_dto->get_artist_review();
				$star   = $review_dto->get_artist_star();
				break;

			case 'album':
				$review = $review_dto->get_album_review();
				$star   = $review_dto->get_album_star();
				break;

			case 'track':
				$review = $review_dto->get_track_review();
				$star   = $review_dto->get_track_star();
				break;
		}


		//---------------------------
		// 必須項目エラーチェック
		//---------------------------
		if (empty($artist_id))
		{
			throw new \Exception('required error[artist_id]', 7002);
		}
		if (empty($user_id))
		{
			throw new \Exception('required error[review_user_id]', 7002);
		}
		//if (empty($is_delete) and empty($review))
		//{
		//	throw new \Exception('required error[review]', 7002);
		//}

		//---------------------------
		// 型チェック
		//---------------------------
		if ( ! is_numeric($artist_id))
		{
			throw new \Exception('type error[artist_id]', 7003);
		}
		if ( ! strlen($artist_id) > 100)
		{
			throw new \Exception('type error[artist_id]', 7003);
		}

		if ( ! is_numeric($user_id))
		{
			throw new \Exception('type error[user_id]',7003);
		}
		if ( ! strlen($user_id) > 100)
		{
			throw new \Exception('type error[user_id over 100]', 7003);
		}

		if ( ! empty($artist_name))
		{
			if (strlen($artist_name) >= 100)
			{
				throw new \Exception('type error[artist_name]', 7003);
			}
		}

		# レビューは2000文字以下
		if (mb_strwidth($review) >= 2000)
		{
			throw new \Exception('type error over 2000[review]', 7003);
		}

		return true;
	}


	public static function get_search()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();

		$page = $artist_dto->get_page();
		if (isset($page) and $page === 0)
		{
			# セッション削除
			\Session::delete('search_artist');
		}

		# 同アーティスト名で前回の検索時0だった場合は検索を行わない（それ以外は検索）
		$obj_search_session = \Session::get('search_artist', new \stdClass());

		if (property_exists($obj_search_session, 'words'))
		{
			if ($obj_search_session->result_zero === false)
			{
				if ($obj_search_session->words === $artist_dto->get_artist_name())
				{
					static::get_search_by_api();
				}
			}
			else
			{
				if ($obj_search_session->words !== $artist_dto->get_artist_name())
				{
					static::get_search_by_api();
				}
			}
		}
		else
		{
			static::get_search_by_api();
		}
	}


	public static function get_lastsearchlist()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();

		$page  = $artist_dto->get_page(); // 2
		$limit = \Config::get('artist.last_search_limit'); //15
		$start = ($page - 1) * $limit; // 1 * 15
		$end   = $page * $limit; // page * 15
		$cnt = 0;
		$arr_list = array();
		foreach (\Cache::get('artist_search_new', array()) as $val)
		{
			if ($cnt >= $start and $cnt <= $end)
			{
				$arr_list[] = $val;
			}
			$cnt++;
		}
		$artist_dto->set_arr_list($arr_list);

		return true;
	}


	public static function get_search_by_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();

		$arr_send = array();
		$arr_send['artist_name'] = $artist_dto->get_artist_name();
		$arr_send['limit']       = 15;
		$arr_send['page']        = 1;
		$arr_send['available_play'] = $artist_dto->get_available_play();

		$obj_session_search = \Session::get('search_artist', new \stdClass());

		# 初検索の場合
		if ( ! property_exists($obj_session_search, 'words'))
		{
			$obj_session_search->words       = '';
			$obj_session_search->times       = 0;
			$obj_session_search->result_zero = false;
			$obj_session_search->timestamp   = \Date::forge()->get_timestamp();
		}

		# 検索名がセッションに格納されていたら2ページ目
		if ($obj_session_search->words === $arr_send['artist_name'])
		{
			$obj_session_search->times = $obj_session_search->times + 1;
			$arr_send['page'] = $obj_session_search->times;
		}
		# 検索名とセッションに格納されている検索文言が違う場合
		else
		{
			$obj_session_search->words = $artist_dto->get_artist_name();
			$obj_session_search->times = 1;
			$obj_session_search->result_zero = false;
			$obj_session_search->timestamp   = \Date::forge()->get_timestamp();
			$arr_send['page'] = 1;
		}

		\Session::set('search_artist', $obj_session_search);

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/artist/search.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();

		if( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiレスポンスが不正です not exist property result');
		}
		if( ! property_exists($obj_response->result, 'arr_list'))
		{
			throw new \Exception('apiレスポンスが不正です not exist property arr_list');
		}

		if (count($obj_response->result->arr_list) === 0)
		{
			\Log::error('apiからのレスポンスは0です');
			$obj_session_search = \Session::get('search_artist');
			$obj_session_search->result_zero = true;
			\Session::set('search_artist', $obj_session_search);

			return true;
		}

		$_arr_response = array();
		foreach ($obj_response->result->arr_list as $i => $obj_gol)
		{
			$image_url        = $obj_gol->image_url;
			$image_small      = $obj_gol->image_small;
			$image_medium     = $obj_gol->image_medium;
			$image_large      = $obj_gol->image_large;
			$image_extralarge = $obj_gol->image_extralarge;

			$_arr_response[$i] = array(
				'id'               => $obj_gol->id,
				'name'             => $obj_gol->name,
				'kana'             => $obj_gol->kana,
				'english'          => $obj_gol->english,
				'mbid_itunes'      => $obj_gol->mbid_itunes,
				'mbid_lastfm'      => $obj_gol->mbid_lastfm,
				'url_itunes'       => $obj_gol->url_itunes,
				'url_lastfm'       => $obj_gol->url_lastfm,
				'image_url'        => empty($image_url)? \Config::get('image.default.artist.origin'): $image_url,
				'image_small'      => empty($image_small)? \Config::get('image.default.artist.small'): $image_small,
				'image_medium'     => empty($image_medium)? \Config::get('image.default.artist.medium'): $image_medium,
				'image_large'      => empty($image_large)? \Config::get('image.default.artist.large'): $image_large,
				'image_extralarge' => empty($image_extralarge)? \Config::get('image.default.artist.extralarge'): $image_extralarge,
				'sort'             => $obj_gol->sort,
			);
		}

		static::$_arr_response = $_arr_response;

		$obj_session_search = \Session::get('search_artist');
		$obj_session_search->result_zero = false;
		\Session::set('search_artist', $obj_session_search);

		return true;
	}


	public static function get_artist_search($type)
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();

		$arr_send = array();
		$arr_send['offset'] = $artist_dto->get_offset();
		$arr_send['limit']  = $artist_dto->get_limit();

		switch ($type)
		{
			case 'new':
				$arr_send['type']   = "new";
				break;

			case 'top':
				$arr_send['type']   = "top";
				break;
			default:
				throw new \Exception('unknown type for get_artist_search api');
		}
		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/artist/getsearch.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();

		if( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiレスポンスが不正です not exist property result');
		}
		if( ! property_exists($obj_response->result, 'arr_list'))
		{
			throw new \Exception('apiレスポンスが不正です not exist property arr_list');
		}
		if (count($obj_response->result->arr_list) === 0)
		{
			\Log::error('apiからのレスポンスは0です');
			return true;
		}

		$artist_dto->set_arr_list($obj_response->result->arr_list);

		return true;
	}


	public static function format_for_dto()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_api_dto = ArtistDto::get_instance();
		$arr_result = static::$_arr_response;

		if (empty($arr_result))
		{
			$artist_api_dto->set_arr_list($arr_result);
			return true;
		}

		foreach ($arr_result as $i => $val)
		{
			$image_url = $val['image_url'];
			if (empty($image_url))
			{
				$default_image_url = \Config::get('image.default.artist.large');
				$arr_result[$i]['image_url'] = $default_image_url;
			}
		}

		$artist_api_dto->set_arr_list($arr_result);

		return true;
	}


	public static function set_session()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto   = UserDto::get_instance();
		$artist_dto = ArtistDto::get_instance();

		$arr_favorite_artists = \Session::get('favorite_artists', array());
		static::$before_count_favorite = count($arr_favorite_artists);

		switch ($artist_dto->get_favorite_status())
		{
			case '0':
				if (empty($arr_favorite_artists)) return true;
				unset($arr_favorite_artists[$artist_dto->get_artist_id()]);
				static::$update_count_favorite = count($arr_favorite_artists);
				\Session::set('favorite_artists', $arr_favorite_artists);
				break;
			case '1':
				$arr_favorite_artists[$artist_dto->get_artist_id()] = $artist_dto->get_artist_id();
				static::$update_count_favorite = count($arr_favorite_artists);
				\Session::set('favorite_artists', $arr_favorite_artists);
				break;
		}

		return true;
	}


	public static function send_api()
	{
		\Log::debug('[start]'. __METHOD__);

		if (static::$before_count_favorite == static::$update_count_favorite)
		{
			\Log::info('すでに登録済みではないか？');
			\Log::info(\Session::get('favorite_artists'));
			//return true;
		}

		$artist_dto = ArtistDto::get_instance();
		$user_dto   = UserDto::get_instance();
		$login_dto  = LoginDto::get_instance();

		$arr_send = array();
		$arr_send['client_user_id']     = $user_dto->get_user_id();
		$arr_send['login_hash']         = $login_dto->get_login_hash();
		$arr_send['favorite_artist_id'] = $artist_dto->get_artist_id();
		$arr_send['status']            =  $artist_dto->get_favorite_status();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/artist/setfavorite.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$result = $obj_api->get_curl_response()->result->favorite_artist_id;

		if ($result === false)
		{
			switch ($artist_dto->get_favorite_status())
			{
				case '0':
					// セッションに戻す
					$arr_favorite_artists = \Session::get('favorite_artists', array());
					$arr_favorite_artists[$artist_dto->get_artist_id()] = $artist_dto->get_artist_id();
					\Session::set('favorite_artists', $arr_favorite_artists);
					break;
				case '1':
					// セッションから除外
					$arr_favorite_artists = \Session::get('favorite_artists', array());
					unset($arr_favorite_artists[$artist_dto->get_artist_id()]);
					\Session::set('favorite_artists', $arr_favorite_artists);
					break;
			}

			throw new Exception('favorite_apiからレスポンスがありません');
		}

		return true;
	}



	public static function _validation_unauthorized_check($client_user_id)
	{
		\Log::debug('[start]'. __METHOD__);

		# セッションからログインユーザ情報を取得
		$arr_user_info = static::_get_user_info_from_session();
		if (empty($arr_user_info['user_id']))
		{
			$message = "ログインがセッションから確認できません";
			Validation::active()->set_message('unauthorized_check', $message);

			return false;
		}

		if ($client_user_id != $arr_user_info['user_id'])
		{
			\Log::info($client_user_id. '!='. $arr_user_info['user_id']);
			$message = "ログインユーザと異なるクライアントユーザでアクセスされました";
			Validation::active()->set_message('unauthorized_check', $message);

			return false;
		}

		return true;
	}
}
