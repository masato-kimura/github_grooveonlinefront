<?php
namespace user\domain\service;

use user\model\dto\UserDto;
use Review\Model\Dto\ReviewMusicDto;
use model\dto\CurlDto;
use util\Api;
use login\model\dto\LoginDto;
use favorite\model\dto\FavoriteUserDto;
use Fuel\Core\Uri;
use model\dto\UserInformationDto;
use Tracklist\Model\Dto\TracklistDto;
use util\Image;
final class UserService
{
	public static function validation_for_you($user_id)
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = \Validation::forge();

		$v = $obj_validation->add('user_id', 'ユーザID');
		$v->add_rule('required');
		$v->add_rule('max_length', 19);
		$v->add_rule('valid_string', array('numeric'));

		$v = $obj_validation->add('page', 'ベージ');
		$v->add_rule('required');
		$v->add_rule('max_length', 19);
		$v->add_rule('numeric_min', 1);
		$v->add_rule('valid_string', array('numeric'));

		$arr_params = array(
			'user_id' => $user_id,
			'page'    => Uri::segment(4,1),
		);
		if ( ! $obj_validation->run($arr_params))
		{
			foreach ($obj_validation->error() as $key => $error)
			{
				throw new \Exception($error->get_message(), 404);
			}
		}

		return true;
	}


	public static function set_dto_for_you($user_id)
	{
		$user_dto   = UserDto::get_instance();
		$review_dto = ReviewMusicDto::get_instance();
		$user_information = UserInformationDto::get_instance();
		$tracklist_dto = TracklistDto::get_instance();

		$user_dto->set_disp_user_id($user_id);
		$tracklist_dto->set_user_id($user_id);
		$review_dto->set_page(uri::segment(4,1));
		$review_dto->set_about('all');
		$review_dto->set_limit(10); // 10件ずつ表示

		// コメントセッション情報
		$arr_user_information = \Session::get('user_information', array());
		if (isset($arr_user_information['comment_count']))
		{
			$user_information->get_comment_count($arr_user_information['comment_count']);
		}
		if (isset($arr_user_information['artist_review_count']))
		{
			$user_information->get_comment_count($arr_user_information['artist_review_count']);
		}

		return true;
	}


	public static function set_dto_from_request($user_id=null)
	{
		\Log::debug('[start]'. __METHOD__);

		$user_id = htmlentities($user_id, ENT_QUOTES, mb_internal_encoding());

		$user_dto = UserDto::get_instance();
		$user_dto->set_user_id($user_id);
		$review_music_dto = ReviewMusicDto::get_instance();
		$review_music_dto->set_review_user_id($user_id);

		foreach (\Input::param() as $i => $val)
		{
			$method_name = 'set_'. $val;
			if (method_exists($user_dto, $method_name))
			{
				$user_dto->$method_name($val);
			}
		}

		return true;
	}


	public static function get_user_info()
	{
		\Log::debug('[start]'. __METHOD__);

		try
		{
			$user_dto = UserDto::get_instance();
			$arr_send = array(
				'user_id' => $user_dto->get_disp_user_id(),
			);

			# CURL送信のための情報をDTOにセット
			$curl_dto = CurlDto::get_instance();
			$curl_dto->set_url(\Config::get('host.api_url'). 'main/user/you.json');
			$curl_dto->set_arr_send($arr_send);

			# CURLにてAPI送信
			$obj_api = new Api();
			$obj_api->send_curl();

			# CURL送信レスポンス
			$obj_response = $obj_api->get_curl_response();
			foreach (get_object_vars($obj_response->result) as $i => $val)
			{
				$method_name = 'set_'. $i;
				if (method_exists($user_dto, $method_name))
				{
					$user_dto->$method_name($val);
				}
			}

			$user_dto->set_favorite_artists($obj_response->result->favorite_artists);

			if (property_exists($obj_response->result, 'track_list'))
			{
				$tracklist_dto = TracklistDto::get_instance();
				$tracklist_dto->set_arr_list($obj_response->result->track_list);
			}

			return false;
		}
		catch (\Exception $e)
		{
			if ($e->getCode() === 9001)
			{
				// ユーザが存在しない
				throw new \Exception($e, 404);
			}

			throw new \Exception($e);
		}
	}


	public static function get_favorite_users()
	{
		\Log::debug('[start]'. __METHOD__);

		try
		{
			$user_dto     = UserDto::get_instance();
			$login_dto    = LoginDto::get_instance();
			$favorite_dto = FavoriteUserDto::get_instance();

			// セッションに格納されているお気に入り登録件数が0の場合はapi送信は実行しない
			if (count(\Session::get('favorite_users')) == 0)
			{
				\Log::info('セッションにないよ');
				//return true;
			}

			$arr_send = array(
				'user_id'    => $user_dto->get_disp_user_id(),
				//'login_hash' => $login_dto->get_login_hash(),
				'offset'     => 0,
				'limit'      => 20,
			);

			# CURL送信のための情報をDTOにセット
			$curl_dto = CurlDto::get_instance();
			$curl_dto->set_url(\Config::get('host.api_url'). 'main/favorite/get.json');
			$curl_dto->set_arr_send($arr_send);

			# CURLにてAPI送信
			$obj_api = new Api();
			$obj_api->send_curl();

			# CURL送信レスポンス
			$obj_response = $obj_api->get_curl_response();

			if (isset($obj_response->result->favorite_users))
			{
				$favorite_dto->set_favorite_users($obj_response->result->favorite_users);
			}

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error($e);
			return true;
		}
	}


	public static function unset_user_information()
	{
		\Log::debug('[start]'. __METHOD__);

		try
		{
			$login_dto = LoginDto::get_instance();

			$user_id    = $login_dto->get_user_id();
			$login_hash = $login_dto->get_login_hash();

			// 未ログイン時はセッションにない
			if (empty($user_id) or empty($login_hash))
			{
				return true;
			}

			\Session::delete('user_information');
			\Session::delete('last_get_user_information');

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error($e);
			return true;
		}
	}

	public static function get_image_profile_url($user_id, $login_hash, $https=false, $size=126)
	{
		return static::_get_user_image_url($user_id, $login_hash, $https, $size);
	}

	public static function get_user_image_url_small($user_id, $https=false)
	{
		return static::_get_user_image_url($user_id, null, $https, '34');
	}

	public static function get_user_image_url_medium($user_id, $https=false)
	{
		return static::_get_user_image_url($user_id, null, $https, '64');
	}

	public static function get_user_image_url_large($user_id, $https=false)
	{
		return static::_get_user_image_url($user_id, null, $https, '126');
	}

	public static function get_user_image_url_extralarge($user_id, $https=false)
	{
		return static::_get_user_image_url($user_id, null, $https, '252');
	}





	private static function _get_user_image_url($user_id, $login_hash=null, $https=false, $size=126)
	{
		$obj_image = new Image();

		if ( ! is_null($login_hash))
		{
			$hash6 = "?". substr($login_hash, 0, 6);
		}
		else
		{
			$hash6 = '';
		}

		if ($https)
		{
			$image_url = \Config::get('host.img_url_https'). 'profile/user/'. $user_id. '/'. $size. '/'. md5($user_id). '.jpg'. $hash6;
			//$image_url = \Config::get('host.img_local_url'). 'profile'. DS. 'user'. DS. $user_id. DS. $size. DS. md5($user_id). '.jpg'. $hash6;
		}
		else
		{
			$image_url = \Config::get('host.img_url_http'). 'profile/user/'. $user_id. '/'. $size. '/'. md5($user_id). '.jpg'. $hash6;
			//$image_url = \Config::get('host.img_local_url'). 'profile'. DS. 'user'. DS. $user_id. DS. $size. DS. md5($user_id). '.jpg'. $hash6;
		}
		return $image_url;
	}


}