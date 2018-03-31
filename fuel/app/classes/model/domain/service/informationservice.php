<?php
namespace model\domain\service;

use Fuel\Core\Session;
use login\model\dto\LoginDto;
use model\dto\CurlDto;
use util\Api;
use model\domain\service\Service;
use model\dto\InformationDto;
use Fuel\Core\Pagination;
use model\dto\UserInformationDto;

final class InformationService extends Service
{
	private static $arr_information = array();
	private static $arr_information_names = array();

	/**
	 * インフォメーションに既読フラグをセット
	 * @param array $info_name　最後に投稿したインフォメーション名
	 * @return boolean
	 */
	public static function set_read_information(array $arr_last_info_name)
	{
		\Log::debug('[start]'. __METHOD__);

		if (\Session::get('information', false))
		{
			$arr_obj_session = \Session::get('information');

			foreach ($arr_last_info_name as $val)
			{
				if (isset($arr_obj_session[$val]))
				{
					$obj_session = $arr_obj_session[$val];
					$obj_session->is_read = true;
					$arr_obj_session[$val] = $obj_session;
				}
			} // endforeach

			\Session::set('information', $arr_obj_session);
		}

		return true;
	}



	/**
	 * インフォメーションが存在し未読の場合は未読フラグをセット
	 * @return 未読数
	 */
	public static function set_unread_information(array $info_name)
	{
		\Log::debug('[start]'. __METHOD__);

		static::$arr_information_names = $info_name;
		// sessionにインフォメーションプロパティが未存在
		if ( ! \Session::get('information', false))
		{
			$obj_session = new \stdClass();
			$obj_session->is_read = false;
			$arr_obj_session = array();
			foreach ($info_name as $val)
			{
				$arr_obj_session[$val] = $obj_session;
			}
			//array('info名' => stdClass('is_read' -> false))
			\Session::set('information', $arr_obj_session);
		}
		else
		{
			$arr_obj_session = \Session::get('information');
			$arr_new_obj_session = array();
			foreach ($info_name as $val)
			{
				// 既存のセッションに存在しない
				if ( ! isset($arr_obj_session[$val]))
				{
					$obj_session = new \stdClass();
					$obj_session->is_read = false;
					$arr_new_obj_session[$val] = $obj_session;
				}
				// 存在する
				else
				{
					// 存在する場合はそのまま
					$arr_new_obj_session[$val] = $arr_obj_session[$val];
				}
			} // endforeach

			// 洗い替え
			\Session::delete('information');
			\Session::set('information', $arr_new_obj_session);
		}

		return true;
	}


	public static function get_user_information_from_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto       = LoginDto::get_instance();
		$information_dto = UserInformationDto::get_instance();

		// 未ログインはユーザインフォメーションを取得しない
		$user_id = $login_dto->get_user_id();
		if (empty($user_id))
		{
			$information_dto->set_count(0);
			return true;
		}

		$now = \Date::forge()->get_timestamp();
		$interval_time = 60 * 15;
//		$interval_time = 60;

		$last_get_timestamp = \Session::get('last_get_user_information', null);
		$arr_user_information = \Session::get('user_information', array());
		$comment_count       = isset($arr_user_information['comment_count'])? $arr_user_information['comment_count']: null;
		$artist_review_count = isset($arr_user_information['artist_review_count'])? $arr_user_information['artist_review_count']: null;
		$information_dto->set_comment_count($comment_count);
		$information_dto->set_artist_reviewt_count($artist_review_count);
		$arr_each_information_count = array_filter($arr_user_information, function($val) {
			if (empty($val)) return false;
			return true;
		});
		// ユーザーインフォメーション件数をDTOに格納
		$information_dto->set_count(count($arr_each_information_count));

		if (empty($last_get_timestamp) or ($last_get_timestamp < ($now - $interval_time)))
		{
			// APIから最新インフォメーション情報を取得
			static::_contact_user_information_from_api();
			\Session::set('last_get_user_information', $now);
		}

		return true;
	}


	private static function _contact_user_information_from_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$information_dto = UserInformationDto::get_instance();
		$login_dto = LoginDto::get_instance();

		// APIへ問い合わせ
		$arr_send = array(
			'user_id'    => $login_dto->get_user_id(),
			'login_hash' => $login_dto->get_login_hash(),
		);

		$url = \Config::get('host.api_url'). 'main/information/getuserinformation.json';

		// CURL送信のためDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url($url);
		$curl_dto->set_arr_send($arr_send);

		// CURLでAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		// CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		if ( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiレスポンスが不正です');
		}

		$information_dto->set_comment_count($obj_response->result->comment_count);
		$information_dto->set_artist_reviewt_count($obj_response->result->artist_review_count);

		$arr_information = array();
		$arr_information['comment_count']       = $information_dto->get_comment_count();
		$arr_information['artist_review_count'] = $information_dto->get_artist_review_count();

		$arr_each_information_count = array_filter($arr_information, function($val) {
			if (empty($val)) return false;
			return true;
		});

		// ユーザーインフォメーション件数をDTOに格納
		$information_dto->set_count(count($arr_each_information_count));

		\Session::set('user_information', $arr_information);

		return true;
	}


	public static function get_information_from_cache(Pagination $pagination=null)
	{
		\Log::debug('[start]'. __METHOD__);

		static::$arr_information = \Cache::get('information');

		$information_dto = InformationDto::get_instance();

		$offset = $pagination->calculated_page - 1;
		if ($offset > 0)
		{
			$offset = ($offset - 1) + $pagination->per_page;
		}
		$end  = $offset + $pagination->per_page;
		$arr_information = array();
		for ($i=$offset; $i<$end; $i++)
		{
			if (empty(static::$arr_information[$i]))
			{
				break;
			}
			$arr_information[] = static::$arr_information[$i];
		}

		$information_dto->set_arr_list($arr_information);

		return true;
	}


	public static function get_information_count()
	{
		\Log::debug('[start]'. __METHOD__);

		$cnt = count(\Cache::get('information'));
		$information_dto = InformationDto::get_instance();
		$information_dto->set_count($cnt);

		return $cnt;
	}


	public static function get_information()
	{
		\Log::debug('[start]'. __METHOD__);

		static::$arr_information = array();
		$arr_send = array(
			'offset' => 0,
			'limit'  => 100,
		);
		$url = \Config::get('host.api_url'). 'main/information/getglobalinformation.json';

		// CURL送信のためDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url($url);
		$curl_dto->set_arr_send($arr_send);

		// CURLでAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		// CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		if ( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiレスポンスが不正です');
		}

		$information_dto = InformationDto::get_instance();
		$information_dto->set_arr_list($obj_response->result->arr_list);
		$information_dto->set_count($obj_response->result->count);

		return true;
	}


	public static function get_last_information_name()
	{
		\Log::debug('[start]'. __METHOD__);

		$information_dto = InformationDto::get_instance();
		$obj_information = current($information_dto->get_arr_list());
		$last_information_name = 'info_'. $obj_information->date;
		$information_dto->set_last_information_name($last_information_name);

		return $last_information_name;
	}


	/**
	 * use static::$arr_information_names に格納済みのこと
	 * @return boolean
	 */
	public static function get_unread_information_count()
	{
		\Log::debug('[start]'. __METHOD__);

		$arr_obj_session = \Session::get('information', false);
		if ( ! $arr_obj_session)
		{
			\Log::info('session none');
			return count(static::$arr_information_names);
		}
		else
		{
			$count = 0;
			foreach (static::$arr_information_names as $val)
			{
				if ( ! isset($arr_obj_session[$val]))
				{
					$count++;
				}
				else
				{
					if (false === $arr_obj_session[$val]->is_read)
					{
						$count++;
					}
				}
			} // endforeach

			return $count;
		}
	}
}
