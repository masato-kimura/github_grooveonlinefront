<?php
use artist\model\dto\ArtistDto;
use Album\Model\Dto\AlbumDto;
use Review\Model\Dto\ReviewMusicDto;
use user\model\dto\UserDto;
use Tracklist\Model\Dto\TracklistDto;
use user\domain\service\UserService;

class View_Artist_Detail extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();
		$artist_dto = ArtistDto::get_instance();
		$album_dto  = AlbumDto::get_instance();
		$user_dto   = UserDto::get_instance();
		$tracklist_dto = TracklistDto::get_instance();

		$this->title           = null;
		$this->description     = null;
		$this->artist_id       = $artist_dto->get_artist_id();
		$this->artist_name     = $artist_dto->get_artist_name();
		$this->artist_image    = $artist_dto->get_image_extralarge();
		$this->arr_list        = $review_dto->get_arr_list();
		$this->arr_album_list  = $album_dto->get_arr_list();
		$this->favorite_status = $artist_dto->get_favorite_status();
		$this->user_id         = $user_dto->get_user_id();
		$this->artist_mbid_itunes  = $artist_dto->get_mbid_itunes();
		$this->artist_mbid_lastfm  = $artist_dto->get_mbid_lastfm();
		$this->artist_segment_name = preg_replace('|^https://itunes.apple.com/jp/artist/([^/]+)/.*$|i', '$1', $artist_dto->get_url_itunes());
		$this->arr_tracklist  = $this->_format_tracklist();
		$this->arr_reviewlist = $this->_format_reviewlist();
		$this->tracklist_id    = $tracklist_dto->get_tracklist_id();
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


	private function _format_reviewlist()
	{
		$review_dto = ReviewMusicDto::get_instance();
		$arr_review_list = array();
		foreach ($review_dto->get_arr_list() as $i => $val)
		{
			$arr_review_list[$i] = $val;
			$arr_review_list[$i]->album_id = isset($val->album_id)? $val->album_id: null;
			$arr_review_list[$i]->track_id = isset($val->track_id)? $val->track_id: null;
			$arr_review_list[$i]->artist_image = ( ! empty($val->image_extralarge))? $val->image_extralarge: 'asset/img//profile/user/default/default.jpg';
			$arr_review_list[$i]->review_user_id = $val->user_id;
			$arr_review_list[$i]->review_user_name = $val->user_name;
			$arr_review_list[$i]->review_user_image = $val->user_image_medium;
			$arr_review_list[$i]->review_id = $val->id;
			$arr_review_list[$i]->star = $val->star;
			$arr_review_list[$i]->updated_at = isset($val->updated_at)? $val->updated_at: null;
			switch ($val->about)
			{
				case 'artist':
					$arr_review_list[$i]->about_name = isset($val->artist_name)? mb_strimwidth($val->artist_name, 0, 80, '...'): null;
					break;
				case 'album':
					$arr_review_list[$i]->about_name = isset($val->about_name)? mb_strimwidth($val->about_name, 0, 80, '...'): null;
					break;
				case 'track':
					$arr_review_list[$i]->about_name = isset($val->about_name)? mb_strimwidth($val->about_name, 0, 80, '...'): null;
					break;
			}
		}
		return $arr_review_list;
	}
}