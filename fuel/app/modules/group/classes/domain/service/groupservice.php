<?php
namespace group\domain\service;

use util;
use user\model\dto\UserDto;
use login\model\dto\LoginDto;
use group\model\dto\GroupDto;
use model\dto\CurlDto;
use util\Api;



final class GroupService
{
	public static function get_cateogry_from_api()
	{
		try
		{
			\Log::debug('[start]'. __METHOD__);

			# CURL送信のための情報をDTOにセット
			$curl_dto = CurlDto::get_instance();
			$curl_dto->set_url(\Config::get('host.api_url'). 'main/category/get.json');
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

			return $obj_response->result->arr_list;
		}
		catch (\Exception $e)
		{
			throw new \Exception($e);
		}
	}


	/**
	 *
	 * @return multitype:
	 */
	public static function check_validation($is_edit=false)
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = \Validation::forge();
		$obj_validation->add('name', 'グループ名')
			->add_rule('required')
			->add_rule('min_length', 1)
			->add_rule('max_length', 30);
		$obj_validation->add('category_id', 'カテゴリー')
			->add_rule('required')
			->add_rule('valid_string', array('numeric'));
		$obj_validation->add('category_name', 'カテゴリー名')
			->add_rule('min_length', 1)
			->add_rule('max_length', 30);
		$obj_validation->add('profile_fields', '紹介文')
			->add_rule('min_length', 0)
			->add_rule('max_length', 2000);

		if ($is_edit)
		{
			$obj_validation->add('group_id')
				->add_rule('required')
				->add_rule('valid_string', array('numeric'));
		}

		$arr_out = array();
		if ( ! $obj_validation->run())
		{
			foreach ($obj_validation->error() as $key => $error)
			{
				$arr_out[$key] = $error;
			}
			return $arr_out;
		}
		return array();
	}


	public static function set_group_to_dto_from_postrequest()
	{
		\Log::debug('[start]'. __METHOD__);

		$arr_request = \Input::post();

		$group_dto  = GroupDto::get_instance();

		$arr_groupdto_class_method  = get_class_methods($group_dto);

		foreach ($arr_groupdto_class_method as $method)
		{
			if (preg_match('/^set_(.+)/', $method, $match))
			{
				if (isset($arr_request[$match[1]]))
				{
					$dto_set_method = "set_". $match[1];
					$group_dto->$dto_set_method($arr_request[$match[1]]);
				}
			}
		}
		unset($method, $match, $dto_set_method);

		return true;
	}


	public static function send_group_to_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto  = UserDto::get_instance();
		$login_dto = LoginDto::get_instance();
		$group_dto = GroupDto::get_instance();

		$arr_send = array();
		$arr_send['group_name'] = $group_dto->get_name();
		$arr_send['user_id'] = $user_dto->get_user_id();
		$arr_send['login_hash'] = $login_dto->get_login_hash();
		$arr_send['chief_user_id'] = $user_dto->get_user_id();
		$arr_send['category_id'] = $group_dto->get_category_id();
		$arr_send['profile_fields'] = $group_dto->get_profile_fields();
		$arr_send['arr_members'] = array( 0 => array('user_id' => $user_dto->get_user_id()));

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/group/create.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		if ( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiレスポンスが不正です');
		}
		$group_dto->set_group_id($obj_response->result->group_id);

		return true;
	}


	public static function send_edit_group_to_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto  = UserDto::get_instance();
		$login_dto = LoginDto::get_instance();
		$group_dto = GroupDto::get_instance();

		$arr_send = array();
		$arr_send['group_id'] = $group_dto->get_group_id();
		$arr_send['group_name'] = $group_dto->get_name();
		$arr_send['category_id'] = $group_dto->get_category_id();
		$arr_send['profile_fields'] = $group_dto->get_profile_fields();
		$arr_send['login_hash'] = $login_dto->get_login_hash();
		$arr_send['arr_members'] = array( 0 => array('user_id' => $user_dto->get_user_id()));
		$arr_send['chief_user_id'] = $user_dto->get_user_id();
		$arr_send['user_id'] = $user_dto->get_user_id();


		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/group/edit.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		if ( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiレスポンスが不正です');
		}

		$group_dto->set_group_id($obj_response->result->group_id);

		return true;
	}

	public static function get_group_info()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto  = UserDto::get_instance();
		$login_dto = LoginDto::get_instance();
		$group_dto = GroupDto::get_instance();

		// @todo @param artist_id only dayo!
		$arr_send = array();
		$arr_send['group_id'] = $group_dto->get_group_id();
		$arr_send['user_id']  = $user_dto->get_user_id();
		$arr_send['login_hash'] = $login_dto->get_login_hash();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/artist/detail.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		if ( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiレスポンスが不正です');
		}

		$obj_result = $obj_response->result;
		$group_dto->set_group_id($obj_result->group_id);
		$group_dto->set_name($obj_result->group_name);
		$group_dto->set_category_id($obj_result->category_id);
		$group_dto->set_category_name($obj_result->category_name);
		$group_dto->set_category_english($obj_result->category_english);
		$group_dto->set_link($obj_result->link);
		$group_dto->set_profile_fields($obj_result->profile_fields);
		$group_dto->set_is_leaved($obj_result->is_leaved);
		$group_dto->set_leave_date($obj_result->leave_date);
		$group_dto->set_members($obj_result->members);

		return true;
	}


	public static function get_image_profile_url($group_id, $https=false)
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$obj_image = new util\Image();

		$hash6 = \Date::forge()->format('%H%M%S');

		if ($https)
		{
			$image_url = \Config::get('host.img_url_https'). 'profile/group/'. $group_id. '/'. md5($group_id). '.jpg?'. $hash6;
			if ( ! $obj_image->url_exists($image_url))
			{
				$image_url = \Config::get('host.img_local_url'). 'profile/group/'. $group_id. '/'. md5($group_id). '.jpg?'. $hash6;
				if ( ! $obj_image->url_exists($image_url))
				{
					$image_url = \Config::get('host.img_local_url'). 'profile/group/default/default.jpg';
				}
			}
		}
		else
		{
			$image_url = \Config::get('host.img_url_http'). 'profile/group/'. $group_id. '/'. md5($group_id). '.jpg?'. $hash6;
			if ( ! $obj_image->url_exists($image_url))
			{
				$image_url = \Config::get('host.img_local_url'). 'profile/group/'. $group_id. '/'. md5($group_id). '.jpg?'. $hash6;
				if ( ! $obj_image->url_exists($image_url))
				{
					$image_url = \Config::get('host.img_local_url'). 'profile/group/default/default.jpg';
				}
			}
		}

		return $image_url;
	}


	public static function send_member_add()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto  = UserDto::get_instance();
		$login_dto = LoginDto::get_instance();
		$group_dto = GroupDto::get_instance();

		$arr_send = array();
		$arr_send['group_id'] = $group_dto->get_group_id();
		$arr_send['user_id']  = $user_dto->get_user_id();
		$arr_send['arr_members'] = $group_dto->get_members();
		$arr_send['login_hash'] = $login_dto->get_login_hash();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/group/memberadd.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();

		return true;
	}


	public static function send_member_delete(array $arr_member_id)
	{
		\Log::debug('[start]'. __METHOD__);

		// 必須項目はuser_id(削除する人), memeber_id(削除される人)、group_id, login_hash値
		// user_idでログインされていて、かつそのuser_idとmember_idは同じグループであること

		$user_dto  = UserDto::get_instance();
		$login_dto = LoginDto::get_instance();
		$group_dto = GroupDto::get_instance();

		$arr_send = array();
		$arr_send['arr_member_id'] = $arr_member_id;
		$arr_send['group_id'] = $group_dto->get_group_id();
		$arr_send['user_id']  = $user_dto->get_user_id();
		$arr_send['login_hash'] = $login_dto->get_login_hash();

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/group/memberdelete.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();

		return true;
	}

}
