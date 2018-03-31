<?php
namespace login\domain\service;

use user\model\dto;

class OauthLoginFacebookConcreteStrategy implements OauthLoginStrategy
{
	private $app_id = "";
	private $secret = "";
	private $redirect_url = "";
	private $access_token = null;

	public function __construct()
	{
		$this->app_id       = \Config::get('oauth.facebook.app_id');
		$this->secret       = \Config::get('oauth.facebook.secret_id');
		$this->redirect_url = \Config::get('oauth.facebook.redirect_url');
	}

	public function get_login_url()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_url = "https://www.facebook.com/v2.8/dialog/oauth?client_id={$this->app_id}&redirect_uri={$this->redirect_url}";

		return $login_url;
	}

	public function login()
	{
		\Log::debug('[start]'. __METHOD__);

		$code = \Input::param('code');
		if (empty($code))
		{
			throw new \Exception('facebook response code is empty');
		}

		$this->access_token = $this->get_access_token($code);
		if ($this->access_token)
		{
			$arr_user_info = (array)$this->get_profile_from_api();
			$user_dto  = \user\model\dto\UserDto::get_instance();
			$login_dto = \login\model\dto\LoginDto::get_instance();

			if ( ! \AddValidation::_validation_valid_reserve_name($arr_user_info['name']))
			{
				$user_dto->set_user_name('Your Name');
				//$user_dto->set_first_name($arr_user_info['first_name']);
				//$user_dto->set_last_name($arr_user_info['last_name']);
			}
			else
			{
				$user_dto->set_user_name($arr_user_info['name']);
				//$user_dto->set_first_name($arr_user_info['first_name']);
				//$user_dto->set_last_name($arr_user_info['last_name']);
			}

			if (isset($arr_user_info['email']))
			{
				$user_dto->set_email($arr_user_info['email']);
			}
			$user_dto->set_birthday('');
			$user_dto->set_profile_fields('');
			if (isset($arr_user_info['gender']))
			{
				$user_dto->set_gender($arr_user_info['gender']);
			}
			if (isset($arr_user_info['link']))
			{
				$user_dto->set_link($arr_user_info['link']);
			}
			if (isset($arr_user_info['localte']))
			{
				$user_dto->set_locale($arr_user_info['locale']);
			}
			if (isset($arr_user_info['cover']))
			{
				if (isset($arr_user_info['cover']->source))
				{
					$user_dto->set_picture($arr_user_info['cover']->source);
				}
			}
			$user_dto->set_auth_type('facebook');
			$user_dto->set_oauth_id($arr_user_info['id']);
			if (\login\domain\service\LoginService::is_auto_login())
			{
				$login_dto->set_auto_login(true);
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	public function logout()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		session_destroy();
		return true;
}

	public function get_request_token()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		return false;
	}

    private function get_access_token($code)
    {
    	\Log::debug('[start]'. __METHOD__);

    	$url = "https://graph.facebook.com/v2.8/oauth/access_token";
    	$curl = \Request::forge($url, 'curl');
    	$curl->set_method('get');
    	$curl->set_params(array(
    			'client_id'     => $this->app_id,
    			'client_secret' => $this->secret,
    			'code'          => $code,
    			'redirect_uri'  => $this->redirect_url
    	));
    	$curl->set_options(array(
    			CURLOPT_RETURNTRANSFER => true,
    			CURLOPT_SSL_VERIFYPEER => false,
    			CURLOPT_TIMEOUT => 60,
    			CURLOPT_CONNECTTIMEOUT => 60,
    			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    	));
    	$curl->execute();
    	$obj_response = json_decode($curl->response());
    	if (empty($obj_response))
    	{
    		throw new \Exception('facebook oauth access_token get error');
    	}
    	return  $obj_response->access_token;
    }

    private function get_profile_from_api()
    {
    	$url  = "https://graph.facebook.com/v2.8/me?fields=id,name,email,gender,locale,cover,link";
    	$curl = \Request::forge($url, 'curl');
    	$curl->set_method('get');
    	$curl->set_params(array(
    			'access_token' => $this->access_token,
    	));
    	$curl->set_options(array(
    			CURLOPT_RETURNTRANSFER => true,
    			CURLOPT_SSL_VERIFYPEER => false,
    			CURLOPT_TIMEOUT => 60,
    			CURLOPT_CONNECTTIMEOUT => 60,
    			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    	));
    	$curl->execute();
    	$obj_response = json_decode($curl->response());
    	if (empty($obj_response))
    	{
    		throw new \Exception('facebook oauth get profile error');
    	}
    	return  $obj_response;
    }

}