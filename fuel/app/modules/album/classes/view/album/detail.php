<?php
use Album\Model\Dto\AlbumDto;
use Review\Model\Dto\ReviewMusicDto;
use Artist\Model\Dto\ArtistDto;
class View_Album_Detail extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();
		$artist_dto = ArtistDto::get_instance();
		$album_dto  = AlbumDto::get_instance();

		$this->title              = null;
		$this->description        = null;
		$this->artist_id          = $artist_dto->get_artist_id();
		$this->artist_name        = $artist_dto->get_artist_name();
		$this->artist_image       = $artist_dto->get_image_extralarge();
		$this->artist_mbid_itunes = $artist_dto->get_mbid_itunes();
		$this->artist_mbid_lastfm = $artist_dto->get_mbid_lastfm();
		$this->album_id           = $album_dto->get_album_id();
		$this->album_name         = $album_dto->get_album_name();
		$this->album_image        = $album_dto->get_image_extralarge();
		$this->album_mbid_itunes  = $album_dto->get_album_mbid_itunes();
		$this->arr_review_list    = $review_dto->get_arr_list();
		$this->arr_tracks         = $album_dto->get_arr_list();
		$this->album_release_itunes = null;
		if (preg_match('/^([\d]+)/', $album_dto->get_release_itunes(), $match))
		{
			if ($match[1] != '0000')
			{
				$this->album_release_itunes = $match[1]. "年発売";
			}
		}

		$this->copyright = $album_dto->get_copyright_itunes();

		if (preg_match('|^https://itunes.apple.com/jp/album/([^/]+)|i', $album_dto->get_album_url_itunes(), $match))
		{
			$this->album_itunes_segment_name = $match[1];
		}
		else
		{
			$this->album_itunes_segment_name = null;
		}

		$this->artist_segment_name  = preg_replace('|^https://itunes.apple.com/jp/artist/([^/]+)/.*$|i', '$1', $artist_dto->get_url_itunes());


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

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}
}