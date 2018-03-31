<?php
use user\model\dto\UserDto;
use util\Display;
use login\model\dto\LoginDto;
use Artist\Model\Dto\ArtistDto;
use Tracklist\Model\Dto\TracklistDto;
class View_Tracklist_Detail extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto    = ArtistDto::get_instance();
		$user_dto      = UserDto::get_instance();
		$login_dto     = LoginDto::get_instance();
		$tracklist_dto = TracklistDto::get_instance();

		$this->login_user_id       = $user_dto->get_user_id();
		$this->tracklist_user_id   = $tracklist_dto->get_user_id();
		$this->tracklist_user_name = $tracklist_dto->get_user_name();
		$this->tracklist_id    = $tracklist_dto->get_tracklist_id();
		$this->tracklist_title = $tracklist_dto->get_title();
		$this->tracklist_created_at = \Date::forge(\Date::forge()->get_timestamp($tracklist_dto->get_created_at()))->format('%Y-%m-%d %H:%M');
		$this->tracklist_updated_at = \Date::forge(\Date::forge()->get_timestamp($tracklist_dto->get_updated_at()))->format('%Y-%m-%d %H:%M');
		$this->loading_artist_search = Display::loading('loading_artist_search');
		$this->loading_album_search  = Display::loading('loading_album_search');
		$this->loading_track_search  = Display::loading('loading_track_search');
		$this->artist_mbid_itunes  = $artist_dto->get_mbid_itunes();
		$this->artist_mbid_lastfm  = $artist_dto->get_mbid_lastfm();
		$this->artist_url_itunes  = $artist_dto->get_url_itunes();
		$this->artist_url_lastfm  = $artist_dto->get_url_lastfm();
		$this->artist_segment_name = preg_replace('|^https://itunes.apple.com/jp/artist/([^/]+)/.*$|i', '$1', $artist_dto->get_url_itunes());

		$this->arr_tracklist = $tracklist_dto->get_arr_list();
		$artist_id = $tracklist_dto->get_artist_id();
		$artist_name = $tracklist_dto->get_artist_name();
		$artist_image = null;
		if ( ! empty($this->arr_tracklist))
		{
			$artist_image = current($this->arr_tracklist)->track_artist_image_extralarge;
		}
		$this->artist_id     = $artist_id;
		$this->artist_name   = $artist_name;
		$this->artist_image  = $artist_image;
	}
}