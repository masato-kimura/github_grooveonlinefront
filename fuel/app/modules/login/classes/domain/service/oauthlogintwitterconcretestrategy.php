<?php
namespace login\domain\service;

use login\model\dto;
use login\domain\sdk;
require_once APPPATH. 'modules/login/classes/domain/sdk/twitteroauth-master/twitteroauth/twitteroauth.php';

class OauthLoginTwitterConcreteStrategy implements OauthLoginStrategy {

    private $twitter;
    private $requestToken;
    private $_oauth_token;
    private $_oauth_token_secret;

    public function __construct()
    {
    }

	public function get_login_url($type=null)
    {
		$twitter = new \TwitterOAuth(\Config::get('oauth.twitter.consumer_key'), \Config::get('oauth.twitter.consumer_secret'));
		if ($type === "new") {
			$requestToken = $twitter->getRequestToken(\Config::get('oauth.twitter.calback_url'). "?type=new");
		}
		else
		{
			$requestToken = $twitter->getRequestToken(\Config::get('oauth.twitter.calback_url'));
		}
		$url = $twitter->getAuthorizeURL($requestToken['oauth_token'], false);
		return $url;
	}


	// twitterログイン情報がOauthUserInfoDtoシングルトンクラスに代入される
	public function login($req=null)
	{
		\Log::debug('[start]'. __METHOD__);

		$connection = new \TwitterOAuth(
								\Config::get('oauth.twitter.consumer_key'),
								\Config::get('oauth.twitter.consumer_secret'),
								\Input::param('oauth_token'),
								\Input::param('oauth_verifier')
						);
		/* Request access tokens from twitter */
		//$access_token = $connection->getAccessToken($req['oauth_verifier']);
		$access_token = $connection->getAccessToken(\Input::param('oauth_verifier'));

		$content = $connection->get('account/verify_credentials');
        //$status = "つぶやきてすと";
        //$connection->oAuthRequest("http://api.twitter.com/1/statuses/update.xml","POST",array("status"=>$status));
        $_SESSION['twitter_access_token'] =  $access_token;
        $user_info = $content;
        $user_dto = \user\model\dto\UserDto::get_instance();
        $login_dto = \login\model\dto\LoginDto::get_instance();

		if ( ! \AddValidation::_validation_valid_reserve_name($user_info->name))
		{
			$user_dto->set_user_name('Your Name');
		}
		else
		{
			$user_dto->set_user_name($user_info->name);
		}

        $user_dto->set_email('');
        $user_dto->set_first_name('');
        $user_dto->set_last_name('');
        $user_dto->set_link($user_info->url);
        $user_dto->set_picture($user_info->profile_image_url);
        $user_dto->set_gender('');
        $user_dto->set_locale($user_info->lang);
        $user_dto->set_profile_fields($user_info->description);
        $user_dto->set_auth_type('twitter');
        $login_dto->set_auth_type('twitter');
        $user_dto->set_oauth_id($user_info->id);
        if (LoginService::is_auto_login())
        {
        	$login_dto->set_auto_login(true);
        }
        return true;
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function get_request_token() {


    }



}