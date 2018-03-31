<?php
namespace login\domain\service;

use user\model\dto\UserDto;
use login\model\dto\LoginDto;
require_once APPPATH.'modules/login/classes/domain/sdk/google-api-php-client/src/Google_Client.php';
require_once APPPATH.'modules/login/classes/domain/sdk/google-api-php-client/src/contrib/Google_Oauth2Service.php';

class OauthLoginGoogleConcreteStrategy implements OauthLoginStrategy {

    private $login_id = null;
    private $google;
    private $scope;

    public function __construct() {
        $config = array(
                'app_id' => \Config::get('oauth.google.app_id'),
                'secret' => \Config::get('oauth.google.secret_id'),
                'redirect_url' => \Config::get('oauth.google.redirect_url'),
                'developer_key' => \Config::get('oauth.google.developer_key'),
        );
        $this->google = new \Google_Client();
        $this->google->setClientId($config['app_id']);
        $this->google->setClientSecret($config['secret']);
        $this->google->setRedirectUri($config['redirect_url']);
        $this->google->setDeveloperKey($config['developer_key']);
        $this->scope = new \Google_Oauth2Service($this->google);
    }

    public function get_login_url()
    {
        return $this->google->createAuthUrl();
    }

	public function login($req = null)
	{
		\Log::debug('[start]'. __METHOD__);

		$this->google->authenticate($req['code']);
		$user_info = $this->scope->userinfo_v2_me->get();

		$user_dto  = UserDto::get_instance();
		$login_dto = LoginDto::get_instance();

		if ( ! \AddValidation::_validation_valid_reserve_name($user_info['name']))
		{
			$user_dto->set_user_name('Your Name');
		}
		else
		{
			$user_dto->set_user_name($user_info['name']);
		}

		$first_name = isset($user_info['given_name'])? $user_info['given_name']: null;
		$last_name  = isset($user_info['family_name'])? $user_info['family_name']: null;
		$email      = isset($user_info['email'])? $user_info['email']: null;
		$gender     = isset($user_info['gender'])? $user_info['gender']: null;
		$link       = isset($user_info['link'])? $user_info['link']: null;
		$locale     = isset($user_info['locale'])? $user_info['locale']: null;
		$picture    = isset($user_info['picture'])? $user_info['picture']: null;

		$user_dto->set_first_name($first_name);
		$user_dto->set_last_name($last_name);
		$user_dto->set_email($email);
		$user_dto->set_birthday('');
		$user_dto->set_profile_fields('');
		$user_dto->set_gender($gender);
		$user_dto->set_link($link);
		$user_dto->set_locale($locale);
		$user_dto->set_picture($picture);
		$user_dto->set_auth_type('google');
		$login_dto->set_auth_type('google');
		$user_dto->set_oauth_id($user_info['id']);

		if (LoginService::is_auto_login())
		{
			$login_dto->set_auto_login(true);
		}

		return true;
	}


    public function logout() {
        session_destroy();
        $this->google->revokeToken();
        return true;
    }

    public function get_request_token() {
        return false;
    }

}