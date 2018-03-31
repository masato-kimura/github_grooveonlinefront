<?php
use Album\Model\Dto\AlbumDto;
use Review\Model\Dto\ReviewMusicDto;
use Track\Model\Dto\TrackDto;
use Artist\Model\Dto\ArtistDto;
class View_Track_Detail extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_dto = ReviewMusicDto::get_instance();
		$artist_dto = ArtistDto::get_instance();
		$album_dto = AlbumDto::get_instance();
		$track_dto = TrackDto::get_instance();

		$this->title        = null;
		$this->description  = null;
		$this->artist_id    = $artist_dto->get_artist_id();
		$this->artist_name  = $artist_dto->get_artist_name();
		$this->artist_image = $artist_dto->get_image_extralarge();
		$this->track_id     = $track_dto->get_track_id();
		$this->track_name   = $track_dto->get_track_name();
		$this->track_image  = $track_dto->get_image_extralarge();
		$this->track_preview_itunes = $track_dto->get_preview_itunes();
		$this->track_mbid_itunes = $track_dto->get_mbid_itunes();

		if (preg_match('/^([\d]+)/', $track_dto->get_release_itunes(), $match))
		{
			$this->track_release_itunes = $match[1]. "年発売";
		}
		else
		{
			$this->track_release_itunes = null;
		}

		if (preg_match('|^https://itunes.apple.com/jp/album/([^/]+)|i', $track_dto->get_url_itunes(), $match))
		{
			$this->track_itunes_segment_name = $match[1];
		}
		else
		{
			$this->track_itunes_segment_name = null;
		}

		$this->arr_review_list = $review_dto->get_arr_list();

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