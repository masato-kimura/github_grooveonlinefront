<?php
use user\model\dto\UserDto;
use login\domain\service\LoginService;
use Review\Model\Dto\ReviewMusicDto;
use favorite\model\dto\FavoriteUserDto;
use model\dto\UserInformationDto;
use Tracklist\Model\Dto\TracklistDto;

class View_User_You extends \ViewModel
{
	public function view()
	{
		$user_dto          = UserDto::get_instance();
		$user_information  = UserInformationDto::get_instance();
		$favorite_user_dto = FavoriteUserDto::get_instance();
		$review_music_dto  = ReviewMusicDto::get_instance();
		$tracklist_dto     = TracklistDto::get_instance();


		// ユーザ情報
		$this->user_id         = $user_dto->get_user_id();
		$this->user_name       = htmlentities($user_dto->get_user_name(), ENT_QUOTES, mb_internal_encoding());
		$this->first_name      = htmlentities($user_dto->get_first_name(), ENT_QUOTES, mb_internal_encoding());
		$this->last_name       = htmlentities($user_dto->get_last_name(), ENT_QUOTES, mb_internal_encoding());
		$this->link            = htmlentities($user_dto->get_link(), ENT_QUOTES, mb_internal_encoding());
		$this->gender          = $user_dto->get_gender();
		$this->old_secret      = $user_dto->get_old_secret();
		$this->old             = $this->_get_old();
		$this->locale          = $user_dto->get_locale();
		$this->country         = $user_dto->get_country();
		$this->pref            = $user_dto->get_pref();
		$this->profile_fields  = htmlentities($user_dto->get_profile_fields(), ENT_QUOTES, mb_internal_encoding());
		$this->facebook_url    = $user_dto->get_facebook_url();
		$this->google_url      = $user_dto->get_google_url();
		$this->twitter_url     = $user_dto->get_twitter_url();
		$this->instagram_url   = $user_dto->get_instagram_url();
		$this->site_url        = $user_dto->get_site_url();
		$this->picture_url     = $user_dto->get_picture_url();
		$this->birthday_secret = $user_dto->get_birthday_secret();
		$this->birthday_year   = $user_dto->get_birthday_year();
		$this->birthday_month  = $user_dto->get_birthday_month();
		$this->birthday_day    = $user_dto->get_birthday_day();
		$this->user_image      = LoginService::get_user_image_url_extralarge($this->user_id);
		$this->thanks          = $user_dto->get_arr_thanks();
		$this->cools           = $user_dto->get_arr_cools();
		$this->favorite_artists = $user_dto->get_favorite_artists();
		$this->review_comment_count = $user_information->get_comment_count();
		$this->artist_review_count  = $user_information->get_artist_review_count();

		$arr_user_info = \Session::get('user_info', array());
		$this->client_user_id  = isset($arr_user_info['user_id'])? $arr_user_info['user_id']: null;
		$this->arr_favorite_user = \Session::get('favorite_users', array());

		// ユーザレビュー情報
		$this->arr_music_review = $review_music_dto->get_arr_list();

		// お気に入りユーザ情報
		$this->favorite_users = $favorite_user_dto->get_favorite_users();

		// トラックリスト
		$this->track_list = $tracklist_dto->get_arr_list();
	}

	private function _get_old()
	{
		$user_dto = UserDto::get_instance();
		$year  = $user_dto->get_birthday_year();
		$month = $user_dto->get_birthday_month();
		$day   = $user_dto->get_birthday_day();
		if (empty($year) or empty($month) or empty($day))
		{
			return '';
		}

		$ymd = $year. sprintf('%02d', $month). sprintf('%02d', $day);
		return (int)((Date::forge()->format('%Y%m%d') - $ymd)/10000);
	}
}