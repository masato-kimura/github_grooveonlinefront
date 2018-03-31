<?php

use Review\model\dto\ReviewMusicDto;
use user\model\dto\UserDto;
use Artist\Model\Dto\ArtistDto;
use login\domain\service\LoginService;
use login\model\dto\LoginDto;
use Review\Model\Dto\CoolDto;
use Album\Model\Dto\AlbumDto;
use Track\Model\Dto\TrackDto;
use Review\Model\Dto\CommentDto;
class View_Music_Detail extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_music_dto = ReviewMusicDto::get_instance();
		$comment_dto      = CommentDto::get_instance();
		$artist_dto       = ArtistDto::get_instance();
		$album_dto        = AlbumDto::get_instance();
		$track_dto        = TrackDto::get_instance();
		$user_dto         = UserDto::get_instance();
		$login_dto        = LoginDto::get_instance();
		$cool_dto         = CoolDto::get_instance();

		$this->id               = current($review_music_dto->get_arr_list())->id;
		$this->about            = $review_music_dto->get_about();
		$this->arr_list         = $review_music_dto->get_arr_list();
		$this->arr_comment_list = $comment_dto->get_arr_list();
		$this->comment_count    = $comment_dto->get_count();// 全件数
		$this->comment_offset   = $comment_dto->get_offset();
		$this->comment_limit    = $comment_dto->get_limit();
		$this->comment_more_flg = false;
		if ($this->comment_count > $comment_dto->get_limit())
		{
			$this->comment_more_flg = true;
		}
		$this->artist_id        = $artist_dto->get_artist_id();
		$this->artist_name      = current($review_music_dto->get_arr_list())->artist_name;
		$this->artist_name      = htmlentities($this->artist_name);
		$this->artist_image     = $artist_dto->get_image_large();
		$this->artist_mbid_itunes   = $artist_dto->get_mbid_itunes();
		$this->artist_mbid_lastfm   = $artist_dto->get_mbid_lastfm();
		$this->artist_segment_name  = preg_replace('|^https://itunes.apple.com/jp/artist/([^/]+)/.*$|i', '$1', $artist_dto->get_url_itunes());
		$this->user_id          = $user_dto->get_user_id();
		$this->user_name        = htmlentities($user_dto->get_user_name(), ENT_QUOTES, mb_internal_encoding());
		$this->user_image       = current($review_music_dto->get_arr_list())->user_image_medium;
		$this->album            = $album_dto->get_arr_list();
		$this->album_id         = isset(current($this->arr_list)->about_id)? current($this->arr_list)->about_id: null;
		$this->album_name       = isset(current($this->arr_list)->about_name)? mb_strimwidth(current($this->arr_list)->about_name, 0, 500, ' ..'): null;
		$this->album_name       = htmlentities($this->album_name);
		$this->album_image      = $album_dto->get_image_medium();
		if (empty($this->album_image))
		{
			$this->album_image = isset(current($this->album)->image_medium) ? current($this->album)->image_medium : null;
		}
		if (isset(current($this->album)->release_itunes) and current($this->album)->release_itunes !== '0000-00-00 00:00:00')
		{
			if (preg_match ('/^[0-9]{4}/', current($this->album)->release_itunes, $match))
			{
				$this->album_release = $match[0]. '年発売';
			}
			else
			{
				$this->album_release = null;
			}
		}
		else if (isset(current($this->album)->release_lastfm) and current($this->album)->release_lastfm !== '0000-00-00 00:00:00')
		{
			if (preg_match ('/^[0-9]{4}/', current($this->album)->release_lastfm, $match))
			{
				$this->album_release = $match[0]. '年発売';
			}
			else
			{
				$this->album_release = null;
			}
		}
		else
		{
			$this->album_release = null;
		}

		if (preg_match('|^https://itunes.apple.com/jp/album/([^/]+)|i', $album_dto->get_album_url_itunes(), $match))
		{
			$this->itunes_segment_name = $match[1];
		}
		else
		{
			$this->itunes_segment_name = null;
		}

		$this->copyright    = $album_dto->get_copyright_itunes();
		$this->genre_itunes = $album_dto->get_genre_itunes();
		$this->track        = $track_dto->get_arr_list();
		$this->track_id     = isset(current($this->arr_list)->about_id)? current($this->arr_list)->about_id: null;
		$this->track_image  = $track_dto->get_image_extralarge();
		$this->track_name   = htmlentities($track_dto->get_track_name());
		$this->track_preview_itunes = $track_dto->get_preview_itunes();
		$this->review       = current($this->arr_list)->review;
		$this->review       = preg_replace('/'. PHP_EOL. '/', '<br />', htmlentities($this->review, ENT_QUOTES, mb_internal_encoding()));
		$this->star         = current($this->arr_list)->star;
		$this->cool_count   = current($this->arr_list)->cool_count;
		if (empty($this->cool_count))
		{
			$this->cool_count = null;
		}
		$this->created_at    = current($this->arr_list)->created_at;
		$this->updated_at    = isset(current($this->arr_list)->updated_at)? current($this->arr_list)->updated_at: null;
		if ($this->created_at === $this->updated_at)
		{
			$this->updated_at = null;
		}

		$this->itunes_segment_name = null;
		$this->mbid_itunes = null;
		switch ($this->about)
		{
			case 'artist':
				$this->about_id     = $this->artist_id;
				$this->about_name   = $this->artist_name;
				$this->about_j_name = 'アーティスト';
				$this->about_image  = $artist_dto->get_image_large();
				$this->artist_mbid_itunes  = $artist_dto->get_mbid_itunes();
				$this->artist_segment_name = preg_replace('|^https://itunes.apple.com/jp/artist/([^/]+)/.*$|i', '$1', $artist_dto->get_url_itunes());
				break;
			case 'album':
				$this->about_id     = $this->album_id;
				$this->about_name   = $this->album_name;
				$this->about_j_name = 'アルバム';
				$this->about_image  = $this->album_image;
				$this->mbid_itunes  = $album_dto->get_album_mbid_itunes();
				if (preg_match('|^https://itunes.apple.com/jp/album/([^/]+)|i', $album_dto->get_album_url_itunes(), $match))
				{
					$this->track_itunes_segment_name = $match[1];
				}
				break;
			case 'track':
				$this->about_id     = $this->track_id;
				$this->about_name   = $this->track_name;
				$this->about_j_name = 'トラック';
				if ($this->track_image)
				{
					$this->about_image = $this->track_image;
				}
				else
				{
					$this->about_image = $this->artist_image;
				}
				$this->mbid_itunes = $track_dto->get_mbid_itunes();
				if (preg_match('|^https://itunes.apple.com/jp/album/([^/]+)|i', $track_dto->get_url_itunes(), $match))
				{
					$this->track_itunes_segment_name = $match[1];
				}
				break;
			default:
				$this->about_id     = null;
				$this->about_name   = null;
				$this->about_j_name = null;
				$this->about_image  = $this->artist_image;
		}

		# プロフィール画像urlを取得
		$login_dto = LoginDto::get_instance();
		$user_id = $login_dto->get_user_id();
		$this->user_me_id   = $login_dto->get_user_id();
		$this->user_me_name = htmlentities($login_dto->get_user_name(), ENT_QUOTES, mb_internal_encoding());

		if ( ! empty($user_id))
		{
			$this->user_me_image = LoginService::get_image_profile_url($login_dto->get_user_id(), $login_dto->get_login_hash(), false);
		}
		else
		{
			$this->user_me_image = null;
		}

		# クールユーザ一覧情報
		$this->arr_cool_users = array();
		foreach ($cool_dto->get_arr_list() as $i => $val)
		{
			$this->arr_cool_users[$i]['user_id']    = $val->user_id;
			$this->arr_cool_users[$i]['user_name']  = htmlentities($val->user_name, ENT_QUOTES, mb_internal_encoding());
			$this->arr_cool_users[$i]['user_image'] = LoginService::get_user_image_url_small($val->user_id);
		}

		# クール済みチェック
		$login_user_id = $login_dto->get_user_id();
		$this->is_cool_done = false;
		if (empty($login_user_id))
		{
			// 未ログイン時
			$arr_cool_session = \Session::get('send_cool');
			$send_cool_id = $review_music_dto->get_about(). '_'. $review_music_dto->get_review_id();
			if (isset($arr_cool_session[$send_cool_id]))
			{
				$this->is_cool_done = true;
			}
			else
			{
				$this->is_cool_done = $cool_dto->get_is_cool_done();
			}
		}
		else
		{
			// ログイン済み
			$this->is_cool_done = $cool_dto->get_is_cool_done();
		}

		$this->cool_all_count = $cool_dto->get_all_count();

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
		$review_music_dto->set_user_image($this->user_image);

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}
}
