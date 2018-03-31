<?php
use user\model\dto\UserDto;
use Artist\Model\Dto\ArtistDto;
use Tracklist\Model\Dto\TracklistDto;
use user\domain\service\UserService;
class View_Tracklist_Artist extends \ViewModel
{
	public function view()
	{
		$artist_dto    = ArtistDto::get_instance();
		$user_dto      = UserDto::get_instance();
		$tracklist_dto = TracklistDto::get_instance();

		$this->title           = null;
		$this->description     = null;
		$this->artist_id       = $artist_dto->get_artist_id();
		$this->artist_name     = $artist_dto->get_artist_name();
		$this->artist_image    = $artist_dto->get_image_extralarge();
		$this->favorite_status = $artist_dto->get_favorite_status();
		$this->user_id         = $user_dto->get_user_id();
		$this->artist_mbid_itunes  = $artist_dto->get_mbid_itunes();
		$this->artist_mbid_lastfm  = $artist_dto->get_mbid_lastfm();
		$this->artist_segment_name = preg_replace('|^https://itunes.apple.com/jp/artist/([^/]+)/.*$|i', '$1', $artist_dto->get_url_itunes());
		$this->arr_tracklist  = $this->_format_tracklist();
		$this->tracklist_id    = $tracklist_dto->get_tracklist_id();
		$this->tracklist_count = $tracklist_dto->get_count();
		$this->page_offset     = $tracklist_dto->get_offset();
	}


	private function _format_tracklist()
	{
		$tracklist_dto = TracklistDto::get_instance();
		$arr_tracklist = array();
		foreach ($tracklist_dto->get_arr_list() as $i => $val)
		{
			$arr_tracklist[$i] = $val;
			$arr_tracklist[$i]->is_tracks_and_more = false;
			$number = 1;
			foreach ($val->arr_tracks as $j => $track)
			{
				if ($number >= 4)
				{
					$arr_tracklist[$i]->is_tracks_and_more = true;
				}
				else
				{
					$val->arr_tracks_list[$j] = $track;
					$number++;
				}
			}
			if ( ! empty($val->user_id))
			{
				$arr_tracklist[$i]->user_name = $val->user_login_name;
				$arr_tracklist[$i]->user_image = UserService::get_user_image_url_small($val->user_id);
			}
			else
			{
				$arr_tracklist[$i]->user_name = $val->user_name;
				$arr_tracklist[$i]->user_image = null;
			}

		}
		return $arr_tracklist;
	}
}