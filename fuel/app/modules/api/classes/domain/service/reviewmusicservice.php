<?php
namespace Api\domain\service;

use Login\Model\Dto\LoginDto;
use Artist\Model\Dto\ArtistDto;
use Album\Model\Dto\AlbumDto;
use Track\Model\Dto\TrackDto;
use Review\Model\Dto\ReviewMusicDto;
use login\domain\service\LoginService;
use Review\Model\Dto\CoolDto;
use model\dto\CurlDto;
use util\Api;
use model\domain\service\Service;
use Fuel\Core\Validation;
use login\domain\service\SessionService;
use user\model\dto\UserDto;
use Review\Model\Dto\UserCommentDto;
use Review\Model\Dto\CommentDto;

final class ReviewMusicService extends Service
{
	public static function set_dto_for_detail($about, $id)
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();
		$cool_dto   = CoolDto::get_instance();

		$review_dto->set_review_id($id);
		$cool_dto->set_review_id($id);

		$review_dto->set_about($about);
		$cool_dto->set_about($about);


	}


	public static function validation_for_set()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデーションで使用するため obj_requestの値を$_POSTにセットする
		static::_set_request_to_post(static::$_obj_request);

		# バリデーションで使用するためユーザセッションを$_POSTにセットする
		static::_set_session_to_post();

		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('user_id', 'ユーザID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('login_hash', 'ログインハッシュ');
		$v->add_rule('exact_length', '32');

		$v = $obj_validate->add('artist_id', 'アーティストID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('artist_name', 'アーティスト名');
		//$v->add_rule('required');
		$v->add_rule('max_length', '100');

		$v = $obj_validate->add('about', 'about');
		$v->add_rule('required');
		$v->add_rule('match_pattern', '/(artist)|(album)|(track)/');

		$v = $obj_validate->add('is_delete', 'is_delete');
		$v->add_rule('valid_string', array('numeric'));
		$is_delete = \Input::post('is_delete');

		$v = $obj_validate->add('review_id');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');
		$review_id = \Input::post('review_id');
		if ( ! empty($is_delete))
		{
			$v->add_rule('required');
		}

		$v = $obj_validate->add('album_id', 'アルバムID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');
		if (\Input::post('about') === 'album' and empty($review_id))
		{
			$v->add_rule('required');
		}

		$v = $obj_validate->add('album_name', 'アルバム名');
		$v->add_rule('max_length', '100');

		$v = $obj_validate->add('track_id', 'トラックID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');
		if (\Input::post('about') === 'track' and empty($review_id))
		{
			$v->add_rule('required');
		}

		$v = $obj_validate->add('track_name', 'トラック名');
		$v->add_rule('max_length', '100');

		$v = $obj_validate->add('review', 'コメントレビュー');
		$v->add_rule('max_length', '2000');
		$review = \Input::post('review');
		$star   = \Input::post('star');
		if (empty($is_delete) and ! isset($star))
		{
			$v->add_rule('required');
		}

		$v = $obj_validate->add('star', 'スターレビュー');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', '0');
		$v->add_rule('numeric_max', '5');
		if (empty($is_delete) and ! isset($review))
		{
			$v->add_rule('required');
		}

		$v = $obj_validate->add('link', 'リンク');
		$v->add_rule('valid_url');

		# バリデート実行
		static::_validate_run($obj_validate);

		return true;
	}


	public static function validation_for_one()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデートで使用するため obj_requestの値を$_POSTにセットする
		static::_set_request_to_post(static::$_obj_request);

		# バリデーションで使用するためユーザセッションを$_POSTにセットする
		static::_set_session_to_post();

		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('user_id', 'ユーザID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('login_hash', 'login_hash');
		$v->add_rule('exact_length', '32');

		$v = $obj_validate->add('about', 'about');
		$v->add_rule('required');
		$v->add_rule('match_pattern', '/(artist)|(album)|(track)/');

		$v = $obj_validate->add('artist_id', 'アーティストID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');
		if (\Input::post('about') === 'artist')
		{
			$v->add_rule('required');
		}

		$v = $obj_validate->add('album_id', 'アルバムID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');
		if (\Input::post('about') === 'album')
		{
			$v->add_rule('required');
		}

		$v = $obj_validate->add('track_id', 'トラックID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');
		if (\Input::post('about') === 'track')
		{
			$v->add_rule('required');
		}

		# バリデート実行
		static::_validate_run($obj_validate);

		return true;
	}


	public static function validation_for_sendcool()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデートで使用するため obj_requestの値を$_POSTにセットする
		static::_set_request_to_post(static::$_obj_request);

		# バリデーションで使用するためユーザセッションを$_POSTにセットする
		static::_set_session_to_post();

		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('about', 'about');
		$v->add_rule('required');
		$v->add_rule('match_pattern', '/(artist)|(album)|(track)/');

		$v = $obj_validate->add('review_id', 'レビューID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('cool_user_id', 'クールユーザID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('login_hash', 'login_hash');
		$v->add_rule('max_length', '32');

		$v = $obj_validate->add('ip', 'IPアドレス');
		$v->add_rule('valid_ip');

		# バリデート実行
		static::_validate_run($obj_validate);

		return true;
	}


	public static function validation_for_getcool()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデートで使用するため obj_requestの値を$_POSTにセットする
		static::_set_request_to_post(static::$_obj_request);

		# バリデーションで使用するためユーザセッションを$_POSTにセットする
		static::_set_session_to_post();

		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('about', 'about');
		$v->add_rule('required');
		$v->add_rule('match_pattern', '/(artist)|(album)|(track)/');

		$v = $obj_validate->add('review_id', 'レビューID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('user_id', 'ユーザID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('login_hash', 'login_hash');
		$v->add_rule('max_length', '32');

		# バリデート実行
		static::_validate_run($obj_validate);

		return true;
	}


	public static function validation_for_setusercomment()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデートで使用するため obj_requestの値を$_POSTにセットする
		static::_set_request_to_post(static::$_obj_request);

		# バリデーションで使用するためユーザセッションを$_POSTにセットする
		static::_set_session_to_post();

		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('about', 'about');
		$v->add_rule('required');
		$v->add_rule('match_pattern', '/(artist)|(album)|(track)/');

		$v = $obj_validate->add('user_id', 'ユーザID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('login_hash', 'login_hash');
		$v->add_rule('max_length', '32');

		$v = $obj_validate->add('priority', '優先度');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', 19);

		$v = $obj_validate->add('user_comment', 'ユーザコメント');
		$v->add_rule('required');
		$v->add_rule('max_length', 2000);

		# バリデート実行
		static::_validate_run($obj_validate);

		return true;
	}


	public static function validation_for_removeusercomment()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデートで使用するため obj_requestの値を$_POSTにセットする
		static::_set_request_to_post(static::$_obj_request);

		# バリデーションで使用するためユーザセッションを$_POSTにセットする
		static::_set_session_to_post();

		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('id', 'id');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));

		# バリデート実行
		static::_validate_run($obj_validate);

		return true;
	}


	public static function validation_for_sendcomment()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデートで使用するため obj_requestの値を$_POSTにセットする
		static::_set_request_to_post(static::$_obj_request);

		# バリデーションで使用するためユーザセッションを$_POSTにセットする
		static::_set_session_to_post();

		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('about', 'about');
		$v->add_rule('required');
		$v->add_rule('match_pattern', '/(artist)|(album)|(track)/');

		$v = $obj_validate->add('review_id', 'review_id');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));

		$v = $obj_validate->add('review_user_id', 'レビューユーザID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('comment_user_id', 'コメントユーザID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('comment', 'コメント');
		$v->add_rule('required');
		$v->add_rule('max_length', '200');

		$v = $obj_validate->add('user_id', 'ログインユーザID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('login_hash', 'login_hash');
		$v->add_rule('required');
		$v->add_rule('max_length', '32');

		# バリデート実行
		static::_validate_run($obj_validate);

		return true;
	}


	public static function validation_for_getcomment()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('about', 'about');
		$v->add_rule('required');
		$v->add_rule('match_pattern', '/(artist)|(album)|(track)/');

		$v = $obj_validate->add('review_id', 'review_id');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));

		$v = $obj_validate->add('offset', 'コメントオフセット');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('limit', 'コメントリミット');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		# バリデート実行
		$arr_params = array(
			'about'          => static::$_obj_request->about,
			'review_id'      => static::$_obj_request->review_id,
			'offset' => static::$_obj_request->offset,
			'limit'  => static::$_obj_request->limit,
		);

		static::_validate_run($obj_validate, $arr_params);

		return true;
	}


	public static function validation_for_getlist()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validate = Validation::forge();

		$login_dto = UserDto::get_instance();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('about', 'about');
		$v->add_rule('match_pattern', '/(artist)|(album)|(track)/');

		$v = $obj_validate->add('artist_id', 'artist_id');
		$v->add_rule('valid_string', array('numeric'));

		$v = $obj_validate->add('offset', 'コメントオフセット');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', 19);

		$v = $obj_validate->add('limit', 'コメントリミット');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', 19);

		$v = $obj_validate->add('user_id', 'user_id');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', 19);

		# バリデート実行
		$arr_params = array(
			'about'     => isset(static::$_obj_request->about)? static::$_obj_request->about: null,
			'artist_id' => isset(static::$_obj_request->artist_id)? static::$_obj_request->artist_id: null,
			'offset'    => static::$_obj_request->offset,
			'limit'     => static::$_obj_request->limit,
			'user_id'   => static::$_obj_request->user_id,
		);

		static::_validate_run($obj_validate, $arr_params);

		return true;
	}


	public static function validation_for_deletecomment()
	{
		\Log::debug('[start]'. __METHOD__);

		# バリデートで使用するため obj_requestの値を$_POSTにセットする
		static::_set_request_to_post(static::$_obj_request);

		# バリデーションで使用するためユーザセッションを$_POSTにセットする
		static::_set_session_to_post();

		$obj_validate = Validation::forge();

		# API共通バリデート設定
		static::_validate_base($obj_validate);

		# 個別バリデート設定
		$v = $obj_validate->add('comment_id', 'comment_id');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));

		$v = $obj_validate->add('user_id', 'ログインユーザID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('max_length', '19');

		$v = $obj_validate->add('login_hash', 'login_hash');
		$v->add_rule('required');
		$v->add_rule('max_length', '32');

		# バリデート実行
		static::_validate_run($obj_validate);

		return true;
	}



	public static function set_dto_for_set()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto   = UserDto::get_instance();
		$login_dto  = LoginDto::get_instance();
		$artist_dto = ArtistDto::get_instance();
		$album_dto  = AlbumDto::get_instance();
		$track_dto  = TrackDto::get_instance();
		$review_dto = ReviewMusicDto::get_instance();

		// from Session
		$arr_user_info = SessionService::get('user_info');
		$login_dto->set_user_id(trim($arr_user_info['user_id']));
		$user_dto->set_user_id(trim($arr_user_info['user_id']));
		$login_dto->set_login_hash(trim($arr_user_info['login_hash']));

		foreach (static::$_obj_request as $key => $val)
		{
			if (empty($val))
			{
				continue;
			}
			if ($key === 'artist_id')
			{
				$review_dto->set_artist_id(trim($val));
				$artist_dto->set_artist_id(trim($val));
			}
			if ($key === 'artist_name')
			{
				$review_dto->set_artist_name(trim($val));
				$artist_dto->set_artist_name(trim($val));
			}
			if ($key === 'about')
			{
				$review_dto->set_about(trim($val));
			}
			if ($key === 'is_delete')
			{
				$review_dto->set_is_delete(trim($val));
			}
			if ($key === 'album_id')
			{
				$review_dto->set_album_id(trim($val));
				$album_dto->set_album_id(trim($val));
			}
			if ($key === 'album_name')
			{
				$review_dto->set_album_name(trim($val));
				$album_dto->set_album_name(trim($val));
			}
			if ($key === 'album_name_hidden')
			{
				$review_dto->set_album_name_hidden(trim($val));
			}
			if ($key === 'track_id')
			{
				$review_dto->set_track_id(trim($val));
				$track_dto->set_track_id(trim($val));
			}
			if ($key === 'track_name')
			{
				$review_dto->set_track_name(trim($val));
				$track_dto->set_track_name(trim($val));
			}
			if ($key === 'track_name_hidden')
			{
				$review_dto->set_track_name_hidden(trim($val));
			}
			if ($key === 'review_id')
			{
				$review_dto->set_review_id(trim($val));
			}
			if ($key === 'review')
			{
				$review_dto->set_review(trim($val));
			}
			if ($key === 'star')
			{
				$review_dto->set_star(trim($val));
			}
			if ($key === 'link')
			{
				$review_dto->set_link(trim($val));
			}
		} // foreach

		return true;
	}


	public static function set_dto_for_one()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto  = UserDto::get_instance();
		$login_dto  = LoginDto::get_instance();
		$artist_dto = ArtistDto::get_instance();
		$album_dto  = AlbumDto::get_instance();
		$track_dto  = TrackDto::get_instance();
		$review_dto = ReviewMusicDto::get_instance();

		// from Session
		$arr_user_info = SessionService::get('user_info');
		$login_dto->set_user_id(trim($arr_user_info['user_id']));
		$user_dto->set_user_id(trim($arr_user_info['user_id']));
		$login_dto->set_login_hash(trim($arr_user_info['login_hash']));

		foreach (static::$_obj_request as $key => $val)
		{
			if (empty($val))
			{
				continue;
			}
			if ($key === 'user_id')
			{
				$user_dto->set_user_id(trim($val));
				$login_dto->set_user_id(trim($val));
			}
			if ($key === 'login_hash')
			{
				$login_dto->set_login_hash(trim($val));
			}
			if ($key === 'about')
			{
				$review_dto->set_about(trim($val));
			}
			if ($key === 'artist_id')
			{
				$review_dto->set_artist_id(trim($val));
				$artist_dto->set_artist_id(trim($val));
			}
			if ($key === 'album_id')
			{
				$review_dto->set_album_id(trim($val));
				$album_dto->set_album_id(trim($val));
			}
			if ($key === 'track_id')
			{
				$review_dto->set_track_id(trim($val));
				$track_dto->set_track_id(trim($val));
			}
		}
	}


	public static function set_dto_for_sendcool()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto   = UserDto::get_instance();
		$login_dto  = LoginDto::get_instance();
		$review_dto = ReviewMusicDto::get_instance();
		$cool_dto   = CoolDto::get_instance();

		// from Session
		$arr_user_info = SessionService::get('user_info');
		$login_dto->set_user_id(trim($arr_user_info['user_id']));
		$user_dto->set_user_id(trim($arr_user_info['user_id']));
		$cool_dto->set_cool_user_id($arr_user_info['user_id']);
		$login_dto->set_login_hash(trim($arr_user_info['login_hash']));

		foreach (static::$_obj_request as $key => $val)
		{
			if (empty($val))
			{
				continue;
			}
			if ($key === 'review_id')
			{
				$review_dto->set_review_id(trim($val));
				$cool_dto->set_review_id(trim($val));
			}
			if ($key === 'about')
			{
				$review_dto->set_about(trim($val));
				$cool_dto->set_about(trim($val));
			}
			if ($key === 'review_user_id')
			{
				$review_dto->set_review_user_id(trim($val));
				$cool_dto->set_review_user_id(trim($val));
			}
		} // endforeach

		$cool_dto->set_ip(\Input::real_ip());
	}


	public static function set_dto_for_getcool()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto   = UserDto::get_instance();
		$login_dto  = LoginDto::get_instance();
		$review_dto = ReviewMusicDto::get_instance();
		$cool_dto   = CoolDto::get_instance();

		// from Session
		$arr_user_info = SessionService::get('user_info');
		$login_dto->set_user_id(trim($arr_user_info['user_id']));
		$user_dto->set_user_id(trim($arr_user_info['user_id']));
		$cool_dto->set_cool_user_id($arr_user_info['user_id']);
		$login_dto->set_login_hash(trim($arr_user_info['login_hash']));

		foreach (static::$_obj_request as $key => $val)
		{
			if (empty($val))
			{
				continue;
			}
			if ($key === 'review_id')
			{
				$cool_dto->set_review_id(trim($val));
			}
			if ($key === 'about')
			{
				$cool_dto->set_about(trim($val));
			}
			if ($key === 'user_id')
			{
				$cool_dto->set_cool_user_id(trim($val));
			}
			if ($key === 'offset')
			{
				$cool_dto->set_offset(trim($val));
			}
			if ($key === 'limit')
			{
				$cool_dto->set_limit(trim($val));
			}
		} // endforeach

		$cool_dto->set_ip(\Input::real_ip());
	}


	public static function set_dto_for_setusercomment()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto        = LoginDto::get_instance();
		$user_dto         = UserDto::get_instance();
		$user_comment_dto = UserCommentDto::get_instance();

		// from Session
		$arr_user_info = SessionService::get('user_info');
		$login_dto->set_user_id(trim($arr_user_info['user_id']));
		$user_dto->set_user_id(trim($arr_user_info['user_id']));
		$user_comment_dto->set_user_id(trim($arr_user_info['user_id']));
		$login_dto->set_login_hash(trim($arr_user_info['login_hash']));

		foreach (static::$_obj_request as $key => $val)
		{
			if (empty($val))
			{
				continue;
			}
			if ($key === 'about')
			{
				$user_comment_dto->set_about(trim($val));
			}
			if ($key === 'user_id')
			{
				$user_comment_dto->set_user_id(trim($val));
				$login_dto->set_user_id(trim($val));
			}
			if ($key === 'login_hash')
			{
				$login_dto->set_login_hash(trim($val));
			}
			if ($key === 'user_comment')
			{
				$user_comment_dto->set_user_comment(trim($val));
			}
			if ($key === 'priority')
			{
				$user_comment_dto->set_priority(trim($val));
			}
		} // endforeach

		return true;
	}


	public static function set_dto_for_removeusercomment()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto        = LoginDto::get_instance();
		$user_dto         = UserDto::get_instance();
		$user_comment_dto = UserCommentDto::get_instance();

		// from Session
		$arr_user_info = SessionService::get('user_info');
		$login_dto->set_user_id(trim($arr_user_info['user_id']));
		$user_dto->set_user_id(trim($arr_user_info['user_id']));
		$user_comment_dto->set_user_id(trim($arr_user_info['user_id']));
		$login_dto->set_login_hash(trim($arr_user_info['login_hash']));

		foreach (static::$_obj_request as $key => $val)
		{
			if (empty($val))
			{
				continue;
			}
			if ($key === 'id')
			{
				$user_comment_dto->set_user_comment_id(trim($val));
			}
			if ($key === 'user_id')
			{
				$user_comment_dto->set_user_id(trim($val));
				$login_dto->set_user_id(trim($val));
			}
			if ($key === 'login_hash')
			{
				$login_dto->set_login_hash(trim($val));
			}
		} // endforeach

		return true;
	}


	public static function set_dto_for_sendcomment()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto   = LoginDto::get_instance();
		$user_dto    = UserDto::get_instance();
		$comment_dto = CommentDto::get_instance();

		// from Session
		$arr_user_info = SessionService::get('user_info');
		$login_dto->set_user_id(trim($arr_user_info['user_id']));
		$login_dto->set_login_hash(trim($arr_user_info['login_hash']));
		$user_dto->set_user_id(trim($arr_user_info['user_id']));
		$comment_dto->set_comment_user_id(trim($arr_user_info['user_id']));

		foreach (static::$_obj_request as $key => $val)
		{
			if (empty($val))
			{
				continue;
			}

			if ($key === 'about')
			{
				$comment_dto->set_about(trim($val));
			}

			if ($key === 'review_id')
			{
				$comment_dto->set_review_id(trim($val));
			}

			if ($key === 'review_user_id')
			{
				$comment_dto->set_review_user_id(trim($val));
			}

			if ($key === 'comment')
			{
				$comment_dto->set_comment(trim($val));
			}

			if ($key === 'comment_offset')
			{
				$comment_dto->set_offset(trim($val));
			}

			if ($key === 'comment_limit')
			{
				$comment_dto->set_limit(trim($val));
			}

		} // endforeach

		return true;
	}


	public static function set_dto_for_deletecomment()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto   = LoginDto::get_instance();
		$user_dto    = UserDto::get_instance();
		$comment_dto = CommentDto::get_instance();

		// from Session
		$arr_user_info = SessionService::get('user_info');
		$login_dto->set_user_id(trim($arr_user_info['user_id']));
		$login_dto->set_login_hash(trim($arr_user_info['login_hash']));
		$user_dto->set_user_id(trim($arr_user_info['user_id']));
		$comment_dto->set_comment_user_id(trim($arr_user_info['user_id']));
		foreach (static::$_obj_request as $key => $val)
		{
			if (empty($val))
			{
				continue;
			}

			if ($key === 'comment_id')
			{
				$comment_dto->set_comment_id(trim($val));
			}
		} // endforeach

		return true;
	}


	public static function set_dto_for_getcomment()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto   = LoginDto::get_instance();
		$user_dto    = UserDto::get_instance();
		$comment_dto = CommentDto::get_instance();

		foreach (static::$_obj_request as $key => $val)
		{
			if ( ! isset($val))
			{
				continue;
			}

			if ($key === 'about')
			{
				$comment_dto->set_about(trim($val));
			}

			if ($key === 'review_id')
			{
				$comment_dto->set_review_id(trim($val));
			}

			if ($key === 'offset')
			{
				$comment_dto->set_offset(trim($val));
			}

			if ($key === 'limit')
			{
				$comment_dto->set_limit(trim($val));
			}
		} // endforeach

		return true;
	}


	public static function set_dto_for_getlist()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto  = ReviewMusicDto::get_instance();

		foreach (static::$_obj_request as $key => $val)
		{
			if ( ! isset($val))
			{
				continue;
			}

			if ($key === 'about')
			{
				$review_dto->set_about(trim($val));
			}

			if ($key === 'artist_id')
			{
				$review_dto->set_artist_id(trim($val));
			}

			if ($key === 'offset')
			{
				$review_dto->set_offset(trim($val));
			}

			if ($key === 'limit')
			{
				$review_dto->set_limit(trim($val));
			}

			if ($key === 'user_id')
			{
				$review_dto->set_review_user_id(trim($val));
			}
		} // endforeach

		return true;
	}



	public static function set_session_tmp_review()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$album_dto  = AlbumDto::get_instance();
		$track_dto  = TrackDto::get_instance();
		$review_dto = ReviewMusicDto::get_instance();

		$obj_tmp_review = new \stdClass();
		$obj_tmp_review->timestamp   = \Date::forge()->get_timestamp();
		$obj_tmp_review->artist_id   = $artist_dto->get_artist_id();
		$obj_tmp_review->artist_name = $artist_dto->get_artist_name();
		$obj_tmp_review->album_id    = $album_dto->get_album_id();
		$obj_tmp_review->album_name  = $album_dto->get_album_name();
		$obj_tmp_review->track_id    = $track_dto->get_track_id();
		$obj_tmp_review->track_name  = $track_dto->get_track_name();
		$obj_tmp_review->about       = $review_dto->get_about();
		$obj_tmp_review->review      = $review_dto->get_review();
		$obj_tmp_review->star        = $review_dto->get_star();

		# セッションに保存
		\Session::set('tmp_review', $obj_tmp_review);

		return true;
	}


	/**
	 * Session::get('tmp_review')
	 *
	 * @return boolean
	 */
	public static function set_session_to_dto()
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_session = \Session::get('tmp_review');

		if (empty($obj_session))
		{
			return false;
		}
		if ($obj_session->artist_id != \Input::param('artist_id'))
		{
			return false;
		}

		$review_dto = ReviewMusicDto::get_instance();
		$album_dto  = AlbumDto::get_instance();
		$track_dto  = TrackDto::get_instance();

		if (property_exists($obj_session, 'artist_id'))
		{
			$review_dto->set_artist_id($obj_session->artist_id);
		}
		if (property_exists($obj_session, 'artist_name'))
		{
			$review_dto->set_artist_name($obj_session->artist_name);
		}
		if (property_exists($obj_session, 'album_id'))
		{
			$review_dto->set_album_id($obj_session->album_id);
			$album_dto->set_album_id($obj_session->album_id);
		}
		if (property_exists($obj_session, 'album_name'))
		{
			$review_dto->set_album_name($obj_session->album_name);
			$album_dto->set_album_name($obj_session->album_name);
		}
		if (property_exists($obj_session, 'track_id'))
		{
			$review_dto->set_track_id($obj_session->track_id);
			$track_dto->set_track_id($obj_session->track_id);
		}
		if (property_exists($obj_session, 'track_name'))
		{
			$review_dto->set_track_name($obj_session->track_name);
			$track_dto->set_track_name($obj_session->track_name);
		}
		if (property_exists($obj_session, 'about'))
		{
			$review_dto->set_about($obj_session->about);
		}

		switch ($review_dto->get_about())
		{
			case 'artist':
				if (property_exists($obj_session, 'review'))
				{
					$review_dto->set_artist_review($obj_session->review);
				}
				if (property_exists($obj_session, 'star'))
				{
					$review_dto->set_artist_star($obj_session->star);
				}
				break;
			case 'album':
				if (property_exists($obj_session, 'review'))
				{
					$review_dto->set_album_review($obj_session->review);
				}
				if (property_exists($obj_session, 'star'))
				{
					$review_dto->set_album_star($obj_session->star);
				}
				break;
			case 'track':
				if (property_exists($obj_session, 'review'))
				{
					$review_dto->set_track_review($obj_session->review);
				}
				if (property_exists($obj_session, 'star'))
				{
					$review_dto->set_track_star($obj_session->star);
				}
				break;
		}

		return true;
	}


	public static function set_object_to_dto($obj_request)
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();
		$cool_dto   = CoolDto::get_instance();
		$album_dto  = AlbumDto::get_instance();
		$track_dto  = TrackDto::get_instance();
		$login_dto = LoginDto::get_instance();

		foreach ($obj_request as $key => $val)
		{
			if ( ! isset($val))
			{
				continue;
			}
			if ($key === 'is_delete')          $review_dto->set_is_delete($val);
			if ($key === 'user_id')            $review_dto->set_review_user_id(trim($val));
			if ($key === 'review_user_id')
			{
				$review_dto->set_review_user_id(trim($val));
				$cool_dto->set_review_user_id(trim($val));
			}
			if ($key === 'review_id')
			{
				$review_dto->set_review_id(trim($val));
				$cool_dto->set_review_id(trim($val));
			}
			if ($key === 'cool_user_id')       $cool_dto->set_cool_user_id(trim($val));
			if ($key === 'artist_id')          $review_dto->set_artist_id(trim($val));
			if ($key === 'artist_name')        $review_dto->set_artist_name(trim($val));
			if ($key === 'artist_kana')        $review_dto->set_artist_kana(trim($val));
			if ($key === 'artist_mbid_itunes') $review_dto->set_artist_mbid_itunes(trim($val));
			if ($key === 'artist_mbid_lastfm') $review_dto->set_artist_mbid_lastfm(trim($val));
			if ($key === 'artist_url')         $review_dto->set_artist_url(trim($val));
			if ($key === 'album_id')           $review_dto->set_album_id(trim($val));
			if ($key === 'album_name')         $review_dto->set_album_name(trim($val));
			if ($key === 'album_name_hidden')  $review_dto->set_album_name_hidden(trim($val));
			if ($key === 'album_mbid_itunes')  $review_dto->set_album_mbid_itunes(trim($val));
			if ($key === 'album_mbid_lastfm')  $review_dto->set_album_mbid_lastfm(trim($val));
			if ($key === 'album_image')        $review_dto->set_album_image(trim($val));
			if ($key === 'album_url')          $review_dto->set_album_url(trim($val));
			if ($key === 'track_id')           $review_dto->set_track_id(trim($val));
			if ($key === 'track_name')         $review_dto->set_track_name(trim($val));
			if ($key === 'track_name_hidden')  $review_dto->set_track_name_hidden(trim($val));
			if ($key === 'track_mbid_itunes')  $review_dto->set_track_mbid_itunes(trim($val));
			if ($key === 'track_mbid_lastfm')  $review_dto->set_track_mbid_lastfm(trim($val));
			if ($key === 'track_url')          $review_dto->set_track_url(trim($val));
			if ($key === 'tracks')             $review_dto->set_tracks($val);
			if ($key === 'content')            $review_dto->set_content(trim($val));
			if ($key === 'link')               $review_dto->set_link(trim($val));
			if ($key === 'about')
			{
				$review_dto->set_about(trim($val));
				$cool_dto->set_about(trim($val));
			}

			switch ($review_dto->get_about())
			{
				case 'artist':
					if ($key === 'review')     $review_dto->set_artist_review(trim($val));
					if ($key === 'star')       $review_dto->set_artist_star(trim($val));
					break;
				case 'album':
					if ($key === 'review')     $review_dto->set_album_review(trim($val));
					if ($key === 'star')       $review_dto->set_album_star(trim($val));
					break;
				case 'track':
					if ($key === 'review')     $review_dto->set_track_review(trim($val));
					if ($key === 'star')       $review_dto->set_track_star(trim($val));
					break;
			}

			if ($key === 'album_image'){ // album
				$small      = preg_replace('/last\.fm\/serve\/[0-9]+s*\//i', 'last.fm/serve/34/', $val);
				$medium     = preg_replace('/last\.fm\/serve\/[0-9]+s*\//i', 'last.fm/serve/64/', $val);
				$large      = preg_replace('/last\.fm\/serve\/[0-9]+s*\//i', 'last.fm/serve/126/', $val);
				$extralarge = preg_replace('/last\.fm\/serve\/[0-9]+s*\//i', 'last.fm/serve/252/', $val);
				$review_dto->set_album_image($val);
				$album_dto->set_image_url($val);
				$album_dto->set_image_small($small);
				$album_dto->set_image_medium($medium);
				$album_dto->set_image_large($large);
				$album_dto->set_image_extralarge($extralarge);
			}

			if ($key === 'image_url'){ // track
				$small      = preg_replace('/last\.fm\/serve\/[0-9]+s*\//i', 'last.fm/serve/34/', $val);
				$medium     = preg_replace('/last\.fm\/serve\/[0-9]+s*\//i', 'last.fm/serve/64/', $val);
				$large      = preg_replace('/last\.fm\/serve\/[0-9]+s*\//i', 'last.fm/serve/126/', $val);
				$extralarge = preg_replace('/last\.fm\/serve\/[0-9]+s*\//i', 'last.fm/serve/252/', $val);
				$review_dto->set_album_image($val);
				$track_dto->set_image_url($val);
				$track_dto->set_image_small($small);
				$track_dto->set_image_medium($medium);
				$track_dto->set_image_large($large);
				$track_dto->set_image_extralarge($extralarge);
			}

		} // foreach

		return true;
	}

	/**
	 * review_dto にカウントやリミットをセットする
	 * @param unknown $pagination
	 */
	public static function set_pagination_to_dto($pagination)
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();
		$review_dto->set_page($pagination->calculated_page);
		$review_dto->set_limit($pagination->per_page);

		return true;
	}


	public static function set_list_request_to_dto(array $arr_error=array())
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();
		$login_dto = LoginDto::get_instance();
		$login_dto->set_user_id($login_dto->get_user_id());
		$review_dto->set_review_user_id(\Input::param('review_user_id'));
		$review_dto->set_artist_name(\Input::param('artist_name'));
		$review_dto->set_artist_id(\Input::param('artist_id'));
		$review_dto->set_artist_mbid_itunes(\Input::param('artist_mbid_itunes'));
		$review_dto->set_artist_mbid_lastfm(\Input::param('artist_mbid_lastfm'));
		$review_dto->set_album_name(\Input::param('album_name'));
		$review_dto->set_album_mbid_itunes(\Input::param('album_mbid_itunes'));
		$review_dto->set_album_mbid_lastfm(\Input::param('album_mbid_lastfm'));
		$review_dto->set_track_name(\Input::param('track_name'));
		$review_dto->set_track_mbid_itunes(\Input::param('track_mbid_itunes'));
		$review_dto->set_track_mbid_lastfm(\Input::param('track_mbid_lastfm'));
		$review_dto->set_link(\Input::param('link'));
		$review_dto->set_page(\Input::param('page'));
		$review_dto->set_limit(\Input::param('limit'));
		$review_dto->set_sort(\Input::param('sort'));
		$review_dto->set_about(\Input::param('about', 'all'));
		$review_dto->set_search_word(\Input::param('search_word'));

		switch ($review_dto->get_about())
		{
			case 'artist':
				$review_dto->set_review_id(\Input::param('review_id'));
				$review_dto->set_artist_review(\Input::param('review'));
				$review_dto->set_artist_star(\Input::param('star'));
				break;
			case 'album':
				$review_dto->set_review_id(\Input::param('review_id'));
				$review_dto->set_album_review(\Input::param('review'));
				$review_dto->set_album_star(\Input::param('star'));
				break;
			case 'track':
				$review_dto->set_review_id(\Input::param('review_id'));
				$review_dto->set_track_review(\Input::param('review'));
				$review_dto->set_track_star(\Input::param('star'));
				break;
		}

		foreach ($arr_error as $i => $val)
		{
			$method = 'set_'. $i. '(null)';
			$review_dto->$method;
			\Log::error($i. '=>'. $val);
		}

		return true;
	}


	public static function send_write_to_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();
		$login_dto  = LoginDto::get_instance();
		$artist_dto = ArtistDto::get_instance();
		$album_dto  = AlbumDto::get_instance();

		$arr_send = array();
		$arr_send['is_delete']   = $review_dto->get_is_delete();
		$arr_send['user_id']     = $login_dto->get_user_id();
		$arr_send['artist_id']   = $artist_dto->get_artist_id();
		$arr_send['artist_name'] = $artist_dto->get_artist_name();
		$arr_send['album_id']    = $album_dto->get_album_id();
		$arr_send['album_name']  = $album_dto->get_album_name();
		$arr_send['track_id']    = $review_dto->get_track_id();
		$arr_send['track_name']  = $review_dto->get_track_name();
		$arr_send['link']        = $review_dto->get_link();
		$arr_send['about']       = $review_dto->get_about();
		$arr_send['login_hash']  = $login_dto->get_login_hash();
		$arr_send['review_id']   = $review_dto->get_review_id();
		$arr_send['review']      = $review_dto->get_review();
		$arr_send['star']        = $review_dto->get_star();

		switch ($arr_send['about'])
		{
			case 'artist':
				break;
			case 'album':
				$album_name_hidden = $review_dto->get_album_name_hidden();
				if ( ! empty($album_name_hidden))
				{
					$arr_send['album_name'] = $album_name_hidden;
				}
				break;
			case 'track':
				$track_name_hidden = $review_dto->get_track_name_hidden();
				if ( ! empty($track_name_hidden))
				{
					$arr_send['track_name']   = $track_name_hidden;
				}
				break;
			default :
		}

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/review/music/write.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$review_dto->set_updated_at(trim($obj_api->get_curl_response()->result->updated_at));
		$review_dto->set_review_id(trim($obj_api->get_curl_response()->result->review_id));

		return true;
	}


	/**
	 * いいぜをapiへ送信する
	 * @throws \Exception
	 * @return boolean
	 */
	public static function send_cool_to_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$cool_dto   = CoolDto::get_instance();

		$arr_send = array();
		$arr_send['about']          = $cool_dto->get_about();
		$arr_send['review_id']      = $cool_dto->get_review_id();
		$arr_send['cool_user_id']   = $cool_dto->get_cool_user_id();
		$arr_send['ip']             = $cool_dto->get_ip();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/review/music/sendcool.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$cool_dto->set_cool_count($obj_api->get_curl_response()->result->cool_count);
		$cool_dto->set_reflection($obj_api->get_curl_response()->result->reflection);

		return true;
	}


	/**
	 * いいぜをapiへ送信する
	 * @throws \Exception
	 * @return boolean
	 */
	public static function get_cool_from_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$cool_dto  = CoolDto::get_instance();
		$login_dto = LoginDto::get_instance();
		$arr_send = array();
		$arr_send['about']     = $cool_dto->get_about();
		$arr_send['review_id'] = $cool_dto->get_review_id();
		$arr_send['user_id']   = $login_dto->get_user_id();
		$arr_send['ip']        = \Input::real_ip();
		$arr_send['offset']    = $cool_dto->get_offset();
		$arr_send['limit']     = $cool_dto->get_limit();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/review/music/getcool.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		$cool_dto->set_arr_list($obj_response->result->arr_list);
		$cool_dto->set_all_count($obj_response->result->all_count);

		return true;
	}


	/**
	 * ユーザコメントをapiへ送信する
	 * @throws \Exception
	 * @return boolean
	 */
	public static function setusercomment_to_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_comment_dto = UserCommentDto::get_instance();
		$login_dto        = LoginDto::get_instance();

		$arr_send = array();
		$arr_send['about']         = $user_comment_dto->get_about();
		$arr_send['priority']      = $user_comment_dto->get_priority();
		$arr_send['user_id']       = $user_comment_dto->get_user_id();
		$arr_send['user_comment']  = $user_comment_dto->get_user_comment();
		$arr_send['login_hash']    = $login_dto->get_login_hash();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/review/music/setusercomment.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$is_done = $obj_api->get_curl_response()->result->is_done;
		$id      = $obj_api->get_curl_response()->result->id;

		$user_comment_dto->set_user_comment_id($id);

		return true;
	}


	/**
	 * ユーザコメント削除情報をapiへ送信する
	 * @throws \Exception
	 * @return boolean
	 */
	public static function removeusercomment_to_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_comment_dto = UserCommentDto::get_instance();
		$login_dto        = LoginDto::get_instance();

		$arr_send = array();
		$arr_send['id']         = $user_comment_dto->get_user_comment_id();
		$arr_send['user_id']    = $user_comment_dto->get_user_id();
		$arr_send['login_hash'] = $login_dto->get_login_hash();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/review/music/removeusercomment.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$is_done = $obj_api->get_curl_response()->result->is_done;

		return true;
	}


	/**
	 * コメント情報をapiへ送信する
	 * @throws \Exception
	 * @return boolean
	 */
	public static function sendcomment_to_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$comment_dto = CommentDto::get_instance();
		$login_dto        = LoginDto::get_instance();

		$arr_send = array();
		$arr_send['about']          = $comment_dto->get_about();
		$arr_send['review_id']      = $comment_dto->get_review_id();
		$arr_send['review_user_id'] = $comment_dto->get_review_user_id();
		$arr_send['comment']        = $comment_dto->get_comment();
		$arr_send['comment_offset'] = 0;
		$arr_send['comment_limit']  = 50;
		$arr_send['user_id']        = $login_dto->get_user_id();
		$arr_send['login_hash']     = $login_dto->get_login_hash();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/review/music/sendcomment.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$comment_dto->set_comment_id($obj_api->get_curl_response()->result->comment_id);
		$comment_dto->set_count($obj_api->get_curl_response()->result->count);
		$comment_dto->set_arr_list($obj_api->get_curl_response()->result->arr_list);

		static::getcomment_format();

		return true;
	}


	/**
	 * コメント情報をapiへ送信する
	 * @throws \Exception
	 * @return boolean
	 */
	public static function deletecomment_to_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$comment_dto = CommentDto::get_instance();
		$login_dto   = LoginDto::get_instance();

		$arr_send = array();
		$arr_send['comment_id'] = $comment_dto->get_comment_id();
		$arr_send['user_id']    = $login_dto->get_user_id();
		$arr_send['login_hash'] = $login_dto->get_login_hash();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/review/music/deletecomment.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$comment_dto->set_comment_id($obj_api->get_curl_response()->result->comment_id);
		$comment_dto->set_count($obj_api->get_curl_response()->result->count);

		return true;
	}


	/**
	 * コメント情報をapiへ送信する
	 * @throws \Exception
	 * @return boolean
	 */
	public static function getcomment_to_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$comment_dto = CommentDto::get_instance();
		$login_dto        = LoginDto::get_instance();

		$arr_send = array();
		$arr_send['about']     = $comment_dto->get_about();
		$arr_send['review_id'] = $comment_dto->get_review_id();
		$arr_send['offset']    = $comment_dto->get_offset();
		$arr_send['limit']     = $comment_dto->get_limit();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/review/music/getcomment.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$comment_dto->set_review_id($obj_api->get_curl_response()->result->review_id);
		$comment_dto->set_count($obj_api->get_curl_response()->result->count);
		$comment_dto->set_arr_list($obj_api->get_curl_response()->result->arr_list);

		return true;
	}


	public static function set_image_url_to_cool_users()
	{
		\Log::debug('[start]'. __METHOD__);

		$cool_dto = CoolDto::get_instance();
		$arr_result = array();
		foreach ($cool_dto->get_arr_list() as $i => $val)
		{
			$img_url = LoginService::get_user_image_url_small($val->user_id);
			$val->image_url = $img_url;
			$arr_result[$i] = $val;
		}

		$cool_dto->set_arr_list($arr_result);

		return true;
	}


	public static function set_cool_to_session()
	{
		\Log::debug('[start]'. __METHOD__);

		$cool_dto = CoolDto::get_instance();

		$user_id = $cool_dto->get_cool_user_id();
		if (empty($user_id))
		{
			$arr_val = \Session::get('send_cool', array());
			$id = $cool_dto->get_about(). '_'. $cool_dto->get_review_id();
			$arr_val[$id] = true;
			\Session::set('send_cool', $arr_val);
		}

		return true;
	}


	public static function getcomment_format()
	{
		\Log::debug('[start]'. __METHOD__);

		$comment_dto = CommentDto::get_instance();
		$arr_list = array();
		foreach ($comment_dto->get_arr_list() as $i => $val)
		{
			$arr_list[$i] = new \stdClass();
			$arr_list[$i]->comment_id = $val->id;
			$arr_list[$i]->comment_user_id    = $val->comment_user_id;
			$arr_list[$i]->comment_user_name  = $val->user_name;
			$arr_list[$i]->comment_user_image = LoginService::get_user_image_url_small($val->comment_user_id);
			$arr_list[$i]->review_user_id     = $val->review_user_id;
			$arr_list[$i]->about              = $val->about;
			$arr_list[$i]->comment            = $val->comment;
			$arr_list[$i]->is_available       = $val->is_available;
			$arr_list[$i]->comment_datetime   = $val->updated_at;
		}

		$comment_dto->set_arr_list($arr_list);

		return true;
	}


	/**
	 * アーティスト、アルバム、トラックレビューを取得する
	 * @param boolean $is_count
	 * @param boolean $mine  自身のレビューを取得: true, 他人のレビューを取得：false
	 */
	public static function get_list_from_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();

		# ユーザ情報の整理
		$arr_send = array();
		$arr_send['offset']    = $review_dto->get_offset();
		$arr_send['limit']     = $review_dto->get_limit();
		$arr_send['about']     = $review_dto->get_about();
		$arr_send['artist_id'] = $review_dto->get_artist_id();
		$arr_send['user_id']   = $review_dto->get_review_user_id();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_arr_send($arr_send);
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/review/music/getlist.json');

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$cnt = $obj_api->get_curl_response()->result->count;
		$arr_list = $obj_api->get_curl_response()->result->arr_list;
		$arr_list = static::_set_review_detail($arr_list);

		$review_dto->set_arr_list($arr_list);
		$review_dto->set_review_count($cnt);

		return true;
	}


	/**
	 * トップレビューを取得する
	 */
	public static function get_top_review()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();

		# ユーザ情報の整理
		$about = $review_dto->get_top_about();
		$count = $review_dto->get_top_limit();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_arr_send(array());
		$curl_dto->set_url(\Config::get('host.api_url'). "main/review/music/top/{$about}/{$count}.json");

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$arr_list = $obj_api->get_curl_response()->result->arr_list;
		$arr_list = static::_set_review_detail($arr_list);
		$review_dto->set_arr_top_list($arr_list);

		return true;
	}


	private static function _set_review_detail(array $arr_list)
	{
		\Log::debug('[start]'. __METHOD__);

		foreach ($arr_list as $i => $val)
		{
			$arr_list[$i]->user_image_small  = LoginService::get_user_image_url_small($val->user_id);
			$arr_list[$i]->user_image_medium = LoginService::get_user_image_url_medium($val->user_id);
			$arr_list[$i]->user_image = $arr_list[$i]->user_image_medium;
			$arr_list[$i]->artist_image = $arr_list[$i]->image_medium;
			$arr_list[$i]->review = mb_strimwidth($val->review, 0, 100, '・・・');
		}
		return $arr_list;
	}

	public static function get_artist_review_one()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();
		$user_id = $review_dto->get_review_user_id();
		if (empty($user_id))
		{
			return true;
		}

		return static::_get_review_one('artist');
	}


	public static function get_review_one()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();
		$artist_dto = ArtistDto::get_instance();
		$album_dto  = AlbumDto::get_instance();
		$track_dto  = TrackDto::get_instance();
		$login_dto = LoginDto::get_instance();
		$user_dto  = UserDto::get_instance();
		$about = $review_dto->get_about();
		$user_comment_dto = UserCommentDto::get_instance();

		$arr_send = array();
		$arr_send['user_id']    = $user_dto->get_user_id();
		$arr_send['login_hash'] = $login_dto->get_login_hash();
		$arr_send['review_id']  = $review_dto->get_review_id();

		if (empty($arr_send['user_id']))
		{
			return true;
		}

		switch ($about)
		{
			case 'artist':
				$arr_send['artist_id']  = $artist_dto->get_artist_id();
				$arr_send['about']      = $about;
				break;
			case 'album':
				$arr_send['album_id']   = $album_dto->get_album_id();
				$arr_send['about']      = $about;
				break;
			case 'track':
				$arr_send['track_id']   = $track_dto->get_track_id();
				$arr_send['about']      = $about;
				break;
		}

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/review/music/one.json');
		$curl_dto->set_arr_send($arr_send);
		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		if ( ! property_exists($obj_api->get_curl_response(), 'result'))
		{
			throw new \Exception('not exist property result from api');
		}

		$obj_result_base = $obj_api->get_curl_response()->result;

		if (empty($obj_result_base))
		{
			$obj_result = array();
			$obj_user_comment = array();
		}
		else
		{
			$obj_result       = $obj_result_base->arr_review;
			$obj_user_comment = $obj_result_base->arr_user_comment;
		}

		if (empty($obj_result) && empty($obj_user_comment))
		{
			return true;
		}


		$review_dto->set_arr_list($obj_result);
		if ( empty($obj_result))
		{
			$obj_result = new \stdClass();
			$obj_result->review = null;
			$obj_result->star = null;
			$obj_result->updated_at = null;
		}

		switch ($review_dto->get_about())
		{
			case 'artist':
				$review_dto->set_artist_review($obj_result->review);
				$review_dto->set_artist_star($obj_result->star);
				$review_dto->set_artist_updated_at($obj_result->updated_at);
				$user_comment_dto->set_arr_comment($obj_user_comment);
				break;
			case 'album':
				$review_dto->set_album_review($obj_result->review);
				$review_dto->set_album_star($obj_result->star);
				$review_dto->set_album_updated_at($obj_result->updated_at);
				break;
			case 'track':
				$review_dto->set_track_review($obj_result->review);
				$review_dto->set_track_star($obj_result->star);
				$review_dto->set_track_updated_at($obj_result->updated_at);
				break;
		}

		return true;
	}


	/**
	 * いいぜ！したユーザ一覧を取得 ＆ 自身がいいぜ！してるか
	 */
	public static function get_cool_users()
	{
		\Log::debug('[start]'. __METHOD__);

		$cool_dto  = CoolDto::get_instance();
		$login_dto = LoginDto::get_instance();
		$arr_send = array();
		$arr_send['about']      = $cool_dto->get_about();
		$arr_send['review_id']  = $cool_dto->get_review_id();
		$arr_send['user_id']    = $login_dto->get_user_id();
		$arr_send['ip']        = \Input::real_ip();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/review/music/getcool.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		$cool_dto->set_arr_list($obj_response->result->arr_list);
		$cool_dto->set_is_cool_done($obj_response->result->is_done);
		$cool_dto->set_all_count($obj_response->result->all_count);

		return true;
	}


	public static function check_validation_write()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$obj_validation = \Validation::forge();

		$obj_validation_about = $obj_validation->add('about', 'レビュータイプ');
		$obj_validation_about->add_rule('max_length', 10);
		$obj_validation_about->add_rule('valid_string', array('alpha'));

		$obj_validate_artist_name = $obj_validation->add('artist_name', 'アーティスト名');
		$obj_validate_artist_name->add_rule('required');
		$obj_validate_artist_name->add_rule('max_length', 100);

		$obj_validation_album_name = $obj_validation->add('album_name', 'アルバム名');
		$obj_validation_album_name->add_rule('max_length', 100);
		if (in_array(\Input::param('about'), array('album')))
		{
			$obj_validation_album_name->add_rule('required');
		}

		$obj_validation_music_name = $obj_validation->add('music_name', '曲名');
		$obj_validation_music_name->add_rule('max_length', 100);
		if (in_array(\Input::param('about'), array('music')))
		{
			$obj_validation_music_name->add_rule('required');
		}

		$obj_validation->add('link', 'リンク')
			->add_rule('max_length', 255);
		$obj_validation->add('review', 'レビュー')
			->add_rule('max_length', 2000);
		$obj_validation->add('star', '評価')
			->add_rule('valid_string', array('numeric'))
			->add_rule('max_length', '1');

		$arr_error = array();
		if ( ! $obj_validation->run())
		{
			foreach ($obj_validation->error() as $key => $error)
			{
				$arr_error[$key] = $error;
				\Log::error($key. '=>'. $error);
			}
		}

		return $arr_error;
	}

	public static function check_validation_request()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$obj_validation = \Validation::forge();
		$obj_validation->add('artist_id', 'アーティストID')
			->add_rule('required')
			->add_rule('max_length', 255)
			->add_rule('valid_string', array('numeric'));
		$obj_validation->add('artist_name', 'アーティスト名')
			->add_rule('max_length', 255);
		$obj_validation->add('artist_kana', 'アーティストカナ')
			->add_rule('max_length', 255);

		$arr_error = array();
		if ( ! $obj_validation->run())
		{
			foreach ($obj_validation->error() as $key => $error)
			{
				$arr_error[$key] = $error;
			}
		}

		return $arr_error;
	}

	public static function check_validation_list()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$obj_validation = \Validation::forge();
		$obj_validation->add('artist_name', 'アーティスト名')
			->add_rule('required')
			->add_rule('max_length', 100);
		$obj_validation->add('album_name', 'アルバム名')
			->add_rule('max_length', 100);
		$obj_validation->add('music_name', '曲名')
			->add_rule('max_length', 100);
		$obj_validation->add('review_id', 'レビューID')
			->add_rule('valid_string', array('numeric'))
			->add_rule('max_length', 20);
		$obj_validation->add('review_user_id', 'レビューユーザID')
			->add_rule('valid_string', array('numeric'))
			->add_rule('max_length', 20);
		$obj_validation->add('page', 'ページ')
			->add_rule('valid_string', array('numeric'));
		$obj_validation->add('limit', 'ページ内表示件数')
			->add_rule('valid_string', array('numeric'));
		$obj_validation->add('sort', 'ソート')
			->add_rule('valid_string', array('numeric', 'alpha'));

		$arr_error = array();
		if ( ! $obj_validation->run())
		{
			foreach ($obj_validation->error() as $key => $error)
			{
				$arr_error[$key] = $error;
			}
		}

		return $arr_error;
	}
}
