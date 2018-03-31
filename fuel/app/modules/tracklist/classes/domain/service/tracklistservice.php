<?php
namespace Tracklist\Domain\Service;

use Artist\Model\Dto\ArtistDto;
use Fuel\Core\Validation;
use login\model\dto\LoginDto;
use model\domain\service\Service;
use Tracklist\Model\Dto\TracklistDto;
use model\dto\CurlDto;
use util\Api;
use user\model\dto\UserDto;
use user\domain\service\UserService;
use Fuel\Tasks\Tracklist;
final class TracklistService extends Service
{
	public static function validation_for_index($page)
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();

		$v = $obj_validation->add('page', 'page');
		$v->add_rule('valid_string', array('numeric'));
		$arr_valid_values['page'] = $page;

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					default:
						throw new \Exception($val->get_message());
				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_artist($artist_id, $page)
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();

		$v = $obj_validation->add('artist_id', 'artist_id');
		$v->add_rule('required');
		$v->add_rule('max_length', '19');
		$v->add_rule('valid_string', array('numeric'));
		$arr_valid_values['artist_id'] = $artist_id;

		$v = $obj_validation->add('page', 'page');
		$v->add_rule('valid_string', array('numeric'));
		$arr_valid_values['page'] = $page;

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					default:
						throw new \Exception($val->get_message());
				} // endswitch
			}// endforeach
		}

		return true;
	}

	public static function validation_for_user()
	{

	}

	public static function validation_for_set()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログイン
		$login_dto = LoginDto::get_instance();
		$user_id = $login_dto->get_user_id();

		$arr_valid_values = array();
		if ( ! empty($user_id))
		{
			$v = $obj_validation->add('user_id', 'user_id');
			$v->add_rule('is_login'); // 独自ルール
			$arr_valid_values['user_id'] = $user_id;
		}

		$v = $obj_validation->add('title', 'title');
		$v->add_rule('required');
		$v->add_rule('max_length', 30);
		$arr_valid_values['title'] = isset(static::$_obj_request->title)? static::$_obj_request->title: null;

		$v = $obj_validation->add('user_name', 'user_name');
		$v->add_rule('max_length', 20);
		$arr_valid_values['user_name'] = isset(static::$_obj_request->user_name)? static::$_obj_request->user_name: null;

		$v = $obj_validation->add('artist_id', 'artist_id');
		$v->add_rule('max_length', '19');
		$v->add_rule('valid_string', array('numeric'));
		$arr_valid_values['artist_id'] = isset(static::$_obj_request->artist_id)? static::$_obj_request->artist_id: null;

		$v = $obj_validation->add('edit_mode', 'edit_mode');
		$v->add_rule('max_length', 1);
		$v->add_rule('valid_string', array('numeric'));
		$arr_valid_values['edit_mode'] = isset(static::$_obj_request->edit_mode)? static::$_obj_request->edit_mode: false;

		$v = $obj_validation->add('tracklist_id', 'tracklist_id');
		$v->add_rule('max_length', 19);
		$v->add_rule('valid_string', array('numeric'));
		$arr_valid_values['tracklist_id'] = isset(static::$_obj_request->tracklist_id)? static::$_obj_request->tracklist_id: null;

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'is_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。ログインユーザが不正です', 7012); // 未ログイン
					default:
						throw new \Exception($val->get_message());
				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_delete()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		// ログイン
		$login_dto = LoginDto::get_instance();
		$user_id = $login_dto->get_user_id();

		$arr_valid_values = array();

		$v = $obj_validation->add('user_id', 'user_id');
		$v->add_rule('required');
		$v->add_rule('is_login'); // 独自ルール
		$arr_valid_values['user_id'] = $user_id;

		$v = $obj_validation->add('tracklist_id', 'トラックリストID');
		$v->add_rule('required');
		$v->add_rule('max_length', '19');
		$v->add_rule('valid_string', array('numeric'));
		$arr_valid_values['tracklist_id'] = static::$_obj_request->tracklist_id;

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'is_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。ログインユーザが不正です', 7012); // 未ログイン
					default:
						throw new \Exception($val->get_message());
				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_create($artist_id=null)
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		$login_dto = LoginDto::get_instance();
		$user_id = $login_dto->get_user_id();

		$arr_valid_values = array();
		if ( ! empty($user_id))
		{
			$v = $obj_validation->add('user_id', 'user_id');
			$v->add_rule('is_login'); // 独自ルール
			$arr_valid_values['user_id'] = $user_id;
		}
		if ( ! empty($tracklist_id))
		{
			$v = $obj_validation->add('tracklist_id', 'tracklist_id');
			$v->add_rule('max_length', 19);
			$v->add_rule('valid_string', array('numeric'));
			$arr_valid_values['tracklist_id'] = \Input::param('tracklist_id');
		}

		$v = $obj_validation->add('artist_id', 'artist_id');
		$v->add_rule('max_length', '19');
		$v->add_rule('valid_string', array('numeric'));
		$arr_valid_values['artist_id'] = $artist_id;

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'is_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。ログインユーザが不正です', 7012); // 未ログイン
					default:
						throw new \Exception($val->get_message());
				} // endswitch
			}// endforeach
		}

		return true;
	}

	public static function validation_for_detail_display($tracklist_id=null)
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('login\domain\service\LoginService'); // 独自設定

		$login_dto = LoginDto::get_instance();
		$user_id = $login_dto->get_user_id();

		$arr_valid_values = array();
		if ( ! empty($user_id))
		{
			$v = $obj_validation->add('user_id', 'user_id');
			$v->add_rule('is_login'); // 独自ルール
			$arr_valid_values['user_id'] = $user_id;
		}

		$v = $obj_validation->add('tracklist_id', 'tracklist_id');
		$v->add_rule('max_length', '19');
		$v->add_rule('valid_string', array('numeric'));
		$arr_valid_values['tracklist_id'] = $tracklist_id;

		if ( ! $obj_validation->run($arr_valid_values))
		{
			foreach ($obj_validation->error() as $i => $error)
			{
				switch ($error->rule)
				{
					case 'is_login':
						\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
						throw new \Exception('不正な遷移。ログインユーザが不正です', 7012); // 未ログイン
					default:
						throw new \Exception($val->get_message());
				} // endswitch
			}// endforeach
		}

		return true;
	}


	public static function validation_for_detail()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = \Validation::forge();
		$v = $obj_validation->add('tracklist_id', 'トラックリストID');
		$v->add_rule('required');
		$v->add_rule('max_length', '19');
		$v->add_rule('valid_string', array('numeric'));

		$v = $obj_validation->add('artist_id', 'アーティストID');
		$v->add_rule('required');
		$v->add_rule('max_length', '19');
		$v->add_rule('valid_string', array('numeric'));

		$arr_params = array(
			'tracklist_id' => static::$_obj_request->tracklist_id,
			'artist_id'    => static::$_obj_request->artist_id,
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

	public static function validation_for_getlist()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = \Validation::forge();

		$v = $obj_validation->add('user_id', 'ユーザID');
		$v->add_rule('max_length', '19');
		$v->add_rule('valid_string', array('numeric'));

		$v = $obj_validation->add('artist_id', 'アーティストID');
		$v->add_rule('max_length', '19');
		$v->add_rule('valid_string', array('numeric'));

		$v= $obj_validation->add('offset', 'オフセット');
		$v->add_rule('max_length', 19);
		$v->add_rule('valid_string', array('numeric'));

		$v= $obj_validation->add('limit', 'リミット');
		$v->add_rule('max_length', 19);
		$v->add_rule('valid_string', array('numeric'));

		$arr_params = array(
			'user_id'   => isset(static::$_obj_request->user_id)? static::$_obj_request->user_id: null,
			'artist_id' => isset(static::$_obj_request->artist_id)? isset(static::$_obj_request->artist_id): null,
			'offset'    => isset(static::$_obj_request->offset)? static::$_obj_request->offset: null,
			'limit'     => isset(static::$_obj_request->limit)? static::$_obj_request->limit: null,
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

	public static function set_dto_for_index($page)
	{
		\Log::debug('[start]'. __METHOD__);

		$tracklist_dto = TracklistDto::get_instance();
		$artist_dto    = ArtistDto::get_instance();

		$tracklist_dto->set_limit(10);
		$offset = ($page - 1) * $tracklist_dto->get_limit();
		$tracklist_dto->set_offset($offset);

		return true;
	}

	public static function set_dto_for_artist($artist_id, $page)
	{
		\Log::debug('[start]'. __METHOD__);

		$tracklist_dto = TracklistDto::get_instance();
		$artist_dto    = ArtistDto::get_instance();

		$tracklist_dto->set_artist_id($artist_id);
		$tracklist_dto->set_limit(10);
		$offset = ($page - 1) * $tracklist_dto->get_limit();
		$tracklist_dto->set_offset($offset);
		$artist_dto->set_artist_id($artist_id);

		return true;
	}

	public static function set_dto_for_user()
	{

	}

	public static function set_dto_for_set()
	{
		\Log::debug('[start]'. __METHOD__);

		$tracklist_dto = TracklistDto::get_instance();
		$tracklist_dto->set_title(static::$_obj_request->title);
		$tracklist_dto->set_user_name(static::$_obj_request->user_name);
		$tracklist_dto->set_arr_list(static::$_obj_request->arr_list);

		if (isset(static::$_obj_request->artist_id))
		{
			$artist_dto = ArtistDto::get_instance();
			$artist_dto->set_artist_id(static::$_obj_request->artist_id);
		}
		if (isset(static::$_obj_request->edit_mode))
		{
			$tracklist_dto->set_edit_mode(static::$_obj_request->edit_mode);
		}
		if (isset(static::$_obj_request->tracklist_id))
		{
			$tracklist_dto->set_tracklist_id(static::$_obj_request->tracklist_id);
		}
	}

	public static function set_dto_for_delete()
	{
		\Log::debug('[start]'. __METHOD__);

		$tracklist_dto = TracklistDto::get_instance();
		$tracklist_dto->set_tracklist_id(static::$_obj_request->tracklist_id);
	}


	public static function set_dto_for_create($artist_id=null)
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$tracklist_dto = TracklistDto::get_instance();

		$artist_dto->set_artist_id($artist_id);
		$tracklist_dto->set_tracklist_id(\Input::param('tracklist_id'));
	}

	public static function set_dto_for_detail_display($tracklist_id=null)
	{
		\Log::debug('[start]'. __METHOD__);

		$tracklist_dto = TracklistDto::get_instance();
		$tracklist_dto->set_tracklist_id($tracklist_id);
	}

	public static function set_dto_for_detail()
	{
		\Log::debug('[start]'. __METHOD__);

		$tracklist_dto = TracklistDto::get_instance();
		$artist_dto    = ArtistDto::get_instance();
		$tracklist_dto->set_tracklist_id(trim(static::$_obj_request->tracklist_id));
		$artist_dto->set_artist_id(trim(static::$_obj_request->artist_id));

		return true;
	}

	public static function set_dto_for_getlist()
	{
		\Log::debug('[start]'. __METHOD__);

		$tracklist_dto = TracklistDto::get_instance();
		$artist_dto    = ArtistDto::get_instance();
		$user_dto      = UserDto::get_instance();

		foreach (get_object_public_vars(static::$_obj_request) as $i => $val)
		{
			switch ($i)
			{
				case 'user_id':
					$tracklist_dto->set_user_id(trim($val));
					break;
				case 'artist_id':
					$artist_dto->set_artist_id(trim($val));
					break;
				case 'offset':
					$tracklist_dto->set_offset(trim($val));
					break;
				case 'limit':
					$tracklist_dto->set_limit(trim($val));
					break;
			}
		}

		return true;
	}


	public static function send_list_to_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto     = LoginDto::get_instance();
		$artist_dto    = ArtistDto::get_instance();
		$tracklist_dto = TracklistDto::get_instance();

		$arr_send = array();
		$arr_send['title'] = $tracklist_dto->get_title();
		$arr_send['user_name']  = $tracklist_dto->get_user_name();
		$arr_send['user_id']    = $login_dto->get_user_id();
		$arr_send['login_hash'] = $login_dto->get_login_hash();
		$arr_send['artist_id']  = $artist_dto->get_artist_id();
		$arr_send['arr_list']   = $tracklist_dto->get_arr_list();
		$arr_send['edit_mode']  = $tracklist_dto->get_edit_mode();
		$arr_send['tracklist_id'] = $tracklist_dto->get_tracklist_id();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/tracklist/set.json');
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

		$tracklist_dto->set_tracklist_id($obj_response->result->tracklist_id);

		return true;
	}


	public static function delete_from_api()
	{
		$tracklist_dto = TracklistDto::get_instance();
		$login_dto     = LoginDto::get_instance();

		$arr_send = array();
		$arr_send['tracklist_id'] = $tracklist_dto->get_tracklist_id();
		$arr_send['user_id']      = $login_dto->get_user_id();
		$arr_send['login_hash']   = $login_dto->get_login_hash();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/tracklist/delete.json');
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

		return true;
	}

	/*
	 * check
	 */
	public static function get_list_from_api()
	{
		$tracklist_dto = TracklistDto::get_instance();
		$user_dto      = UserDto::get_instance();
		$artist_dto    = ArtistDto::get_instance();

		$arr_send = array();
		$arr_send['offset']    = $tracklist_dto->get_offset();
		$arr_send['limit']     = $tracklist_dto->get_limit();
		$arr_send['artist_id'] = $artist_dto->get_artist_id();
		$arr_send['user_id']   = $tracklist_dto->get_user_id();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/tracklist/getlist.json');
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
		$tracklist_dto->set_count($obj_response->result->count);
		$arr_list = array();
		foreach ($obj_response->result->arr_list as $val)
		{
			if (empty($val->user_name))
			{
				$val->user_name = $val->user_login_name;
				$val->user_image = UserService::get_user_image_url_medium($val->user_id);
			}
			$arr_list[] = $val;

		}
		$tracklist_dto->set_arr_list($arr_list);

		return true;
	}


	public static function get_detail_for_update()
	{
		\Log::info(__METHOD__);

		$login_dto = LoginDto::get_instance();
		$user_id = $login_dto->get_user_id();
		if (empty($user_id))
		{
			return true;
		}

		static::get_detail_from_api();
		$tracklist_dto = TracklistDto::get_instance();
		if (current($tracklist_dto->get_arr_list())->user_id != $user_dto)
		{
			$tracklist_dto->set_arr_list(array());
			$tracklist_dto->set_user_id(null);
		}

		return true;
	}


	public static function get_detail_from_api()
	{
		\Log::info(__METHOD__);

		$tracklist_dto = TracklistDto::get_instance();
		$user_dto      = UserDto::get_instance();
		$artist_dto    = ArtistDto::get_instance();

		$arr_send = array();
		$arr_send['tracklist_id'] = $tracklist_dto->get_tracklist_id();
		$arr_send['artist_id']    = $artist_dto->get_artist_id();
		if (empty($arr_send['tracklist_id']))
		{
			return true;
		}

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/tracklist/getdetail.json');
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
		if (count($obj_response->result->arr_list) === 0)
		{
			throw new \Exception('review not found', 404);
		}

		$tracklist_dto->set_title($obj_response->result->title);
		$tracklist_dto->set_user_name($obj_response->result->user_name);
		$tracklist_dto->set_created_at($obj_response->result->created_at);
		$tracklist_dto->set_updated_at($obj_response->result->updated_at);
		$tracklist_dto->set_artist_id($obj_response->result->artist_id);
		$tracklist_dto->set_artist_name($obj_response->result->artist_name);
		$tracklist_dto->set_arr_list($obj_response->result->arr_list);
		$tracklist_dto->set_user_id($obj_response->result->user_id);
		$tracklist_dto->set_user_name($obj_response->result->user_name);
		$artist_dto->set_mbid_itunes($obj_response->result->artist_mbid_itunes);
		$artist_dto->set_mbid_lastfm($obj_response->result->artist_mbid_lastfm);

		return true;
	}


	public static function _validation_valid_user_id_from_ajax($user_id)
	{
		if (empty($user_id))
		{
			return true;
		}

		$login_dto = LoginDto::get_instance();
		$user_dto = UserDto::get_instance();
		if ($user_dto->get_user_id() == $user_id )
		{
			return true;
		}
		\Log::error($user_dto->get_user_id());
		$message = $user_id. 'がログインユーザと異なります。'. $user_dto->get_user_id();
		Validation::active()->set_message('valid_user_id_from_ajax', $message);

		return false;
	}
}
