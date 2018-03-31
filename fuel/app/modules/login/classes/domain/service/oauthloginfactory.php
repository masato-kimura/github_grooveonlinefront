<?php
namespace login\domain\service;

class OauthLoginFactory {
    private $oauth_name;
    public function __construct() {

    }
    public function set_oauth_type($oauth_name) {
        $this->oauth_name = $oauth_name;
        switch ($this->oauth_name) {
            case 'facebook':
                $oauth_login = new OauthLoginFacebookConcreteStrategy();
                $oauth_login_context = new OauthLoginContext($oauth_login);
                break;
            case 'twitter':
                $oauth_login = new OauthLoginTwitterConcreteStrategy();
                $oauth_login_context = new OauthLoginContext($oauth_login);
                break;
            case 'google':
                $oauth_login = new OauthLoginGoogleConcreteStrategy();
                $oauth_login_context = new OauthLoginContext($oauth_login);
                break;
            case 'yahoo':
                $oauth_login = new OauthLoginYahooConcreteStrategy();
                $oauth_login_context = new OauthLoginContext($oauth_login);
                break;
            default :
                return false;
        }
        return $oauth_login_context;
    }
}

