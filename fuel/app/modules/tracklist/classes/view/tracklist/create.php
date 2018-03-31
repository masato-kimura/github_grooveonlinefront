<?php
use user\model\dto\UserDto;
use util\Display;
use login\model\dto\LoginDto;
use Artist\Model\Dto\ArtistDto;
use Tracklist\Model\Dto\TracklistDto;
class View_Tracklist_Create extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$user_dto   = UserDto::get_instance();
		$login_dto  = LoginDto::get_instance();
		$artist_dto = ArtistDto::get_instance();
		$tracklist_dto = TracklistDto::get_instance();

		$this->user_id      = $login_dto->get_user_id();
		$this->artist_name  = $artist_dto->get_artist_name();
		$this->artist_id    = null;
		if ( ! empty($this->artist_name))
		{
			$this->artist_id    = $artist_dto->get_artist_id();
		}

		$this->artist_image = $artist_dto->get_image_large();

		$tracklist_id = $tracklist_dto->get_tracklist_id();
		if ( ! empty($tracklist_id))
		{
			$this->edit_mode = true;
		}
		else
		{
			$this->edit_mode = false;
		}
		$this->tracklist_id       = $tracklist_id;
		$this->tracklist_arr_list = $tracklist_dto->get_arr_list();
		$this->tracklist_title    = $tracklist_dto->get_title();
		$this->tracklist_username = $tracklist_dto->get_user_name();
		$this->tracklist_artist_id  = $tracklist_dto->get_artist_id();
		$this->tracklist_artistname = $tracklist_dto->get_artist_name();

		$this->loading_artist_search = Display::loading('loading_artist_search');
		$this->loading_album_search  = Display::loading('loading_album_search');
		$this->loading_track_search  = Display::loading('loading_track_search');

		$this->last_search_more_flg = false;
		$this->arr_last_search = $this->_get_last_search();
	}

	private function _get_last_search()
	{
		$arr_list = \Cache::get('artist_search_new');
		$cnt = 0;
		$limit = \Config::get('artist.last_search_limit');
		$arr_result = array();
		foreach ($arr_list as $i => $val)
		{
			if ($cnt > $limit)
			{
				$this->last_search_more_flg = true;
				break;
			}

			$arr_result[$i] = $val;
			$cnt++;
		}

		return $arr_result;
	}
}