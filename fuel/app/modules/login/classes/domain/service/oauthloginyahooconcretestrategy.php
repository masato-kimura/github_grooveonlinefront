<?php
namespace login\domain\service;

require_once APPPATH. 'modules/login/classes/domain/sdk/yconnect_php_sdk/lib/YConnect.inc';

class OauthLoginYahooConcreteStrategy implements OauthLoginStrategy {

    private $yahoo;

    public function __construct() {
        // クレデンシャルインスタンス生成
        $cred = new \ClientCredential(
        			\Config::get('oauth.yahoo.app_id'),
        			\Config::get('oauth.yahoo.secret_id'));
        // YConnectクライアントインスタンス生成
        $this->yahoo = new \YConnectClient($cred);
    }

    public function get_login_url($type=null) {
        $response_type = \OAuth2ResponseType::CODE;
        $scope = array(
                \OIDConnectScope::OPENID,
                \OIDConnectScope::PROFILE,
                \OIDConnectScope::EMAIL,
                \OIDConnectScope::ADDRESS
        );
        $display = \OIDConnectDisplay::DEFAULT_DISPLAY;
        $prompt = array(\OIDConnectPrompt::DEFAULT_PROMPT);
        if ($type === "new") {
            $redirect_uri = \Config::get('oauth.yahoo.redirect_url'). "?type=new";
        } else {
            $redirect_uri = \Config::get('oauth.yahoo.redirect_url');
        }
        $this->yahoo->requestAuth(
                $redirect_uri,
                \Config::get('oauth.yahoo.state'),
                \Config::get('oauth.yahoo.nonce'),
                $response_type,
                $scope,
                $display,
                $prompt
        );
    }

	// ヤフーログイン情報がOauthUserInfoDtoシングルトンクラスに代入される
	public function login($req=null)
	{
		\Log::debug('[start]'. __METHOD__);

        if ($code_result = $this->yahoo->getAuthorizationCode($req['state']))
        {
            $this->yahoo->requestAccessToken(\Config::get('oauth.yahoo.redirect_url'), $code_result);
            $this->yahoo->requestUserInfo($this->yahoo->getAccessToken());
            $arr_user_info = $this->yahoo->getUserInfo();
            $user_dto = \user\model\dto\UserDto::get_instance();
            $login_dto = \login\model\dto\LoginDto::get_instance();

			if ( ! \AddValidation::_validation_valid_reserve_name($arr_user_info['name']))
			{
				$user_dto->set_user_name('Your Name');
			}
			else
			{
				$user_dto->set_user_name($arr_user_info['name']);
			}

            $user_dto->set_first_name($arr_user_info['given_name']);
            $user_dto->set_last_name($arr_user_info['family_name']);
            $user_dto->set_email($arr_user_info['email']);
            $user_dto->set_birthday('birthday');
            $user_dto->set_profile_fields('');
            $user_dto->set_gender($arr_user_info['gender']);
            $user_dto->set_link('');
            $user_dto->set_locale($arr_user_info['locale']);
            $user_dto->set_country($arr_user_info['address']['country']);
            $user_dto->set_postal_code($arr_user_info['address']['postal_code']);
            $user_dto->set_pref($arr_user_info['address']['region']);
            $user_dto->set_locale($arr_user_info['address']['locality']);
            $user_dto->set_picture('');
            $user_dto->set_auth_type('yahoo');
            $login_dto->set_auth_type('yahoo');
            $user_dto->set_oauth_id($arr_user_info['user_id']);
            if (LoginService::is_auto_login()){
            	$login_dto->set_auto_login(true);
            }
            return true;
        } else {
            return false;
        }
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function get_request_token() {


    }



}