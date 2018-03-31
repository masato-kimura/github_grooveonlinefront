<?php
namespace login\domain\service;

interface OauthLoginStrategy {
    // return loginUrl
    public function login();
    public function logout();
    public function get_login_url();
    public function get_request_token();
}