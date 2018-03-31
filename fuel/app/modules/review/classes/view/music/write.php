<?php
use Login\Model\Dto\LoginDto;
use Artist\Model\Dto\ArtistDto;
use Album\Model\Dto\AlbumDto;
use Review\Model\Dto\ReviewMusicDto;
use Track\Model\Dto\TrackDto;
use Review\Model\Dto\UserCommentDto;
class View_Music_Write extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto        = LoginDto::get_instance();
		$review_dto       = ReviewMusicDto::get_instance();
		$artist_dto       = ArtistDto::get_instance();
		$album_dto        = AlbumDto::get_instance();
		$track_dto        = TrackDto::get_instance();
		$arr_list                   = $review_dto->get_arr_list();
		$this->about                = $review_dto->get_about();
		$this->artist_id            = $artist_dto->get_artist_id();
		$this->artist_name          = htmlentities($artist_dto->get_artist_name(), ENT_QUOTES, mb_internal_encoding());
		$this->artist_review_id     = "";
		$this->artist_review        = $review_dto->get_artist_review();
		$this->artist_review_tmp    = null;
		$this->artist_star          = $review_dto->get_artist_star();
		$this->artist_star_tmp      = null;
		$this->artist_image_small   = $artist_dto->get_image_small();
		$this->artist_image_middle  = $artist_dto->get_image_extralarge();
		$this->artist_updated_at    = $review_dto->get_artist_updated_at();
		$this->artist_mbid_itunes   = $artist_dto->get_mbid_itunes();
		$this->artist_mbid_lastfm   = $artist_dto->get_mbid_lastfm();
		$this->artist_segment_name  = preg_replace('|^https://itunes.apple.com/jp/artist/([^/]+)/.*$|i', '$1', $artist_dto->get_url_itunes());
		$this->link                 = $review_dto->get_link();
		$this->album_id             = '';
		$this->album_name           = '';
		$this->album_review_id      = '';
		$this->album_review         = '';
		$this->album_star           = '';
		$this->album_mbid_itunes    = '';
		$this->album_mbid_lastfm    = '';
		$this->album_image          = '';
		$this->album_image_middle   = '';
		$this->album_url_itunes     = '';
		$this->album_url_lastfm     = '';
		$this->album_tracks         = array();
		$this->track_id             = '';
		$this->track_name           = '';
		$this->url_itunes           = '';
		$this->url_lastfm           = '';
		$this->track_review_id      = '';
		$this->track_review         = '';
		$this->track_star           = '';
		$this->track_artist_name    = '';
		$this->track_album_id       = '';
		$this->track_album_name     = '';
		if ( ! empty($arr_list->id)) // APIから取得したアーティスト情報
		{
			$this->artist_review_id  = $arr_list->id;
			$this->artist_review     = $arr_list->review;
			$this->artist_star       = $arr_list->star;
			$this->artist_updated_at = preg_replace('/:[0-9]*$/', '', $arr_list->updated_at);
		}

		$this->is_first_regist =  $login_dto->get_is_first_regist();

		$about = $this->about;
		if (empty($about))
		{
			$this->about = 'artist';
		}

		switch ($this->about)
		{
			case 'artist':
				$review_id = $this->artist_review_id;
				if ( ! empty($review_id))
				{
					$this->artist_review_tmp = $this->artist_review;
					$this->artist_star_tmp   = $this->artist_star;
				}
				else
				{
					$this->artist_review     = $review_dto->get_artist_review();
					$this->artist_star       = $review_dto->get_artist_star();
				}
				break;
			case 'album':
				$this->album_id              = $album_dto->get_album_id();
				$this->album_name            = htmlentities($album_dto->get_album_name(), ENT_QUOTES, mb_internal_encoding());
				$this->album_mbid_itunes     = $album_dto->get_album_mbid_itunes();
				$this->album_mbid_lastfm     = $album_dto->get_album_mbid_lastfm();
				$this->album_image           = $album_dto->get_image_url();
				$this->album_image_small     = $album_dto->get_image_small();
				$this->album_image_middle    = $album_dto->get_image_extralarge();
				$this->album_url_itunes      = $album_dto->get_album_url_itunes();
				$this->album_url_lastfm      = $album_dto->get_album_url_lastfm();
				$this->album_tracks          = $album_dto->get_arr_list();
				$this->album_review_id       = "";
				$this->album_review          = $review_dto->get_album_review();
				$this->album_star            = $review_dto->get_album_star();
				break;
			case 'track':
				$this->track_id             = $track_dto->get_track_id();
				$this->track_name           = htmlentities($track_dto->get_track_name(), ENT_QUOTES, mb_internal_encoding());
				$this->track_mbid_itune     = $track_dto->get_album_mbid_itunes();
				$this->mbid_lastfm          = $track_dto->get_album_mbid_lastfm();
				$this->track_image          = $track_dto->get_image_url();
				$this->track_image_small    = $track_dto->get_image_small();
				$this->track_image_middle   = $track_dto->get_image_extralarge();
				$this->url_itunes           = $track_dto->get_url_itunes();
				$this->url_lastfm           = $track_dto->get_url_lastfm();
				$this->track_review_id      = "";
				$this->track_review         = $review_dto->get_track_review();
				$this->track_star           = $review_dto->get_track_star();
				$this->track_artist_name    = htmlentities($artist_dto->get_artist_name(), ENT_QUOTES, mb_internal_encoding());
				$this->track_album_id       = $album_dto->get_album_id();
				$this->track_album_name     = htmlentities($album_dto->get_album_name(), ENT_QUOTES, mb_internal_encoding());
				break;
			default:
		}

		$user_comment_dto = UserCommentDto::get_instance();
		$this->arr_user_comment = $user_comment_dto->get_arr_comment();
		$this->arr_user_comment = empty($this->arr_user_comment) ? array(): $this->arr_user_comment;
		$this->arr_default_comment = array(
				'大好き',
				'最高！！',
				'イイゼ',
				'視聴中 ♪♪',
				'Now Listening ♪',
				'ヘビーローテション中♪♪',
				);
		//krsort($this->arr_default_comment);

		$this->arr_all_comment = array();

		$cnt = 0;
		krsort($this->arr_user_comment);
		foreach ($this->arr_user_comment as $i => $val_user)
		{
			$this->arr_all_comment[] = array(
				'id' => $val_user->id,
				'user_comment' => $val_user->user_comment,
			);
			$cnt++;
			if ($cnt >= 20)
			{
				break;
			}
		}

		if ($cnt < 20)
		{
			foreach ($this->arr_default_comment as $i => $val_user)
			{
				$this->arr_all_comment[] = array(
					'id' => null,
					'user_comment' => $val_user,
				);
				$cnt++;
				if ($cnt >= 20)
				{
					break;
				}
			}
		}

		$this->arr_star_review = array('0' => '選択してください', '1' => '★', '2' => '★★', '3' => '★★★', '4' => '★★★★', '5' => '★★★★★');
		$this->loading = "
			<div class='loading'>
				<span class='loading'>Loading</span>
				<br />
				<span class='l-1'></span>
				<span class='l-2'></span>
				<span class='l-3'></span>
				<span class='l-4'></span>
				<span class='l-5'></span>
				<span class='l-6'></span>
			</div>";

		// ログインユーザ情報
		$arr_user_info = \Session::get('user_info');
		$this->client_user_id = $arr_user_info['user_id'];
		$this->arr_favorite_artist = \Session::get('favorite_artists', array());
		$this->favorite_status = $artist_dto->get_favorite_status();


	}
}