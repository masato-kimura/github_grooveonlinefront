<?php
namespace user\model\dto;

use model\dto\BaseDto;
class UserDto extends BaseDto
{
    private static $instance = null;

    private $user_id;
    private $disp_user_id;
    private $user_name;
    private $date;
    private $first_name;
    private $last_name;
    private $password; // dummy
    private $password_digits;
    private $email;
    private $link;
    private $gender;   // male femail unspecifed
    private $birthday; // YYYY-mm-dd
    private $birthday_year; //
    private $birthday_month; //
    private $birthday_day;
    private $birthday_secret;
    private $old;
    private $old_secret;
    private $locale;
    private $country;
    private $postal_code; // 郵便番号
    private $pref; // 都道府県
    private $locality; // 市区町村
    private $street; // 番地建物名
    private $profile_fields;
    private $facebook_url = null;
    private $twitter_url = null;
    private $google_url = null;
    private $instagram_url = null;
    private $site_url = null; // ユーザブログurl
    private $group;
    private $auth_type;
    private $oauth_id;
    private $picture_url;
    private $postal_code1; // dummy
    private $postal_code2; // dummy
    private $tmp_image_url;
    private $picture;
    private $member_type; // 契約タイプ 0:free , 1:有料会員, 2:特別会員
    private $arr_cools = array();
    private $arr_thanks = array();
    private $favorite_artists = array();

    private function __construct() {

    }

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set_user_id($user_id) {
        $this->user_id = $user_id;
    }
    public function get_user_id() {
        return $this->user_id;
    }

    public function set_disp_user_id($user_id) {
    	$this->disp_user_id = $user_id;
    }
    public function get_disp_user_id() {
    	return $this->disp_user_id;
    }

    public function set_user_name($user_name) {
        $this->user_name = $user_name;
    }
    public function get_user_name() {
        return $this->user_name;
    }

    public function set_date($date) // よくわからんぞ
    {
    	$this->date = $date;
    }
    public function get_date()
    {
    	return $this->date;
    }

    public function set_first_name($first_name) {
        $this->first_name = $first_name;
    }
    public function get_first_name() {
        return $this->first_name;
    }

    public function set_last_name($last_name) {
        $this->last_name = $last_name;
    }
    public function get_last_name() {
        return $this->last_name;
    }

    // dummy
    public function set_password($password)
    {
    	$this->password = $password;
    }
    public function get_password()
    {
    	return $this->password;
    }

    // dummy
    public function set_password_digits($password_digits)
    {
    	$this->password_digits = $password_digits;
    }
    public function get_password_digits()
    {
    	return $this->password_digits;
    }

    public function set_email($email) {
        $this->email = $email;
    }
    public function get_email() {
        return $this->email;
    }

    public function set_link($link) {
        $this->link = $link;
    }
    public function get_link() {
        return $this->link;
    }

    public function set_gender($gender) {
        $this->gender = $gender;
    }
    public function get_gender() {
        return $this->gender;
    }

    public function set_birthday($birthday) {
        $this->birthday = $birthday;
    }
    public function get_birthday() {
        return $this->birthday;
    }

    public function set_birthday_year($birthday_year) {
        $this->birthday_year = $birthday_year;
    }
    public function get_birthday_year() {
        return $this->birthday_year;
    }

    public function set_birthday_month($birthday_month) {
        $this->birthday_month = $birthday_month;
    }
    public function get_birthday_month() {
        return $this->birthday_month;
    }

    public function set_birthday_day($birthday_day) {
        $this->birthday_day = $birthday_day;
    }
    public function get_birthday_day() {
        return $this->birthday_day;
    }

    public function set_old($old)
    {
    	$this->old = $old;
    }
    public function get_old()
    {
    	return $this->old;
    }

	public function set_birthday_secret($birthday_secret)
	{
		$this->birthday_secret = $birthday_secret;
	}
	public function get_birthday_secret()
	{
		return $this->birthday_secret;
	}

	// dummy
	public function set_old_secret($old_secret)
	{
		$this->old_secret = $old_secret;
	}
	public function get_old_secret() {
		return $this->old_secret;
	}

	public function set_locale($locale) {
        $this->locale = $locale;
    }
    public function get_locale() {
        return $this->locale;
    }

    public function set_country($country) {
        $this->country = $country;
    }
    public function get_country() {
        return $this->country;
    }

    public function set_postal_code($postal_code) {
        $this->postal_code = $postal_code;
    }
    public function get_postal_code() {
        return $this->postal_code;
    }

    public function set_pref($pref) {
        $this->pref = $pref;
    }
    public function get_pref() {
        return $this->pref;
    }

    public function set_locality($locality) {
        $this->locality = $locality;
    }
    public function get_locality() {
        return $this->locality;
    }

    public function set_street($street) {
        $this->street = $street;
    }
    public function get_street() {
        return $this->street;
    }

    public function set_profile_fields($profile_fields) {
        $this->profile_fields = $profile_fields;
    }
    public function get_profile_fields() {
        return $this->profile_fields;
    }

    public function set_facebook_url($str)
    {
    	$this->facebook_url = $str;
    	return true;
    }
    public function get_facebook_url()
    {
    	return $this->facebook_url;
    }

    public function set_twitter_url($str)
    {
    	$this->twitter_url = $str;
    	return true;
    }
    public function get_twitter_url()
    {
    	return $this->twitter_url;
    }

    public function set_google_url($str)
    {
    	$this->google_url = $str;
    	return true;
    }
    public function get_google_url()
    {
    	return $this->google_url;
    }

    public function set_instagram_url($str)
    {
    	$this->instagram_url = $str;
    	return true;
    }
    public function get_instagram_url()
    {
    	return $this->instagram_url;
    }

    public function set_site_url($str)
    {
    	$this->site_url = $str;
    	return true;
    }
    public function get_site_url()
    {
    	return $this->site_url;
    }

    public function set_group($group)
    {
    	$this->group = $group;
    }
    public function get_group()
    {
    	return $this->group;
    }

    public function set_auth_type($auth_type) {
        $this->auth_type = $auth_type;
    }
    public function get_auth_type() {
        return $this->auth_type;
    }

    public function set_oauth_id ($oauth_id) {
        $this->oauth_id = $oauth_id;
    }
    public function get_oauth_id() {
        return $this->oauth_id;
    }

    public function set_picture_url($picture_url)
    {
    	$this->picture_url = $picture_url;
    }
    public function get_picture_url()
    {
    	return $this->picture_url;
    }

    public function set_member_type($str)
    {
    	$this->member_type = $srt;
    }
    public function get_member_type()
    {
    	return $this->member_type;
    }

    public function set_tmp_image_url($tmp_image_url)
    {
        $this->tmp_image_url = $tmp_image_url;
    }
    public function get_tmp_image_url()
    {
        return $this->tmp_image_url;
    }

    public function set_picture($picture)
    {
    	$this->picture = $picture;
    }
    public function get_picture()
    {
    	return $this->picture;
    }

    // dummy
    public function set_postal_code1($postal_code1)
    {
    	$this->postal_code1 = $postal_code1;
    }
    public function get_postal_code1()
    {
        return $this->postal_code1;
    }

    // dummy
    public function set_postal_code2($postal_code2)
    {
    	$this->postal_code2 = $postal_code2;
    }
    public function get_postal_code2()
    {
        return $this->postal_code2;
    }

    public function set_arr_cools($val)
    {
    	$this->arr_cools = $val;
    }
    public function get_arr_cools()
    {
    	return $this->arr_cools;
    }

	public function set_arr_thanks($val)
    {
        $this->arr_thanks = $val;
    }
    public function get_arr_thanks()
    {
		return $this->arr_thanks;
    }

    public function set_favorite_artists($val)
    {
    	$this->arr_favorite_artists = $val;
    }
    public function get_favorite_artists()
    {
    	return $this->arr_favorite_artists;
    }

}
