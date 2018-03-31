<?php

use Review\model\dto\ReviewMusicDto;
class View_Music_Index extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_music_dto = ReviewMusicDto::get_instance();

		$this->about       = $review_music_dto->get_about();
		$this->search_word = $review_music_dto->get_search_word();
		$this->arr_list    = $this->_format_reviewlist();
		$this->artist_id   = $review_music_dto->get_artist_id();
		$this->page_offset = $review_music_dto->get_offset();

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}

	private function _format_reviewlist()
	{
		$review_music_dto = ReviewMusicDto::get_instance();
		$arr_reviewlist = array();
		foreach ($review_music_dto->get_arr_list() as $j => $list)
		{
			$arr_reviewlist[$j] = $list;
			$arr_reviewlist[$j]->artist_id = isset($list->artist_id)? $list->artist_id: null;
			$arr_reviewlist[$j]->artist_name = isset($list->artist_name)? mb_strimwidth($list->artist_name, 0, 25, '...'): null;
			$arr_reviewlist[$j]->artist_image = empty($list->image_medium)? '/asset/img/profile/user/default/default.jpg': $list->image_medium;
			$arr_reviewlist[$j]->user_image = $list->user_image_medium;
			$arr_reviewlist[$j]->review = mb_strimwidth($list->review, 0, 100, '・・・');
			switch ($list->about)
			{
				case 'artist':
					$about_name = isset($list->artist_name)? mb_strimwidth($list->artist_name, 0, 25, '...'): null;
					break;
				case 'album':
					$about_name = isset($list->about_name)? mb_strimwidth($list->about_name, 0, 35, '...'): null;
					break;
				case 'track':
					$about_name = isset($list->about_name)? mb_strimwidth($list->about_name, 0, 35, '...'): null;
					break;
				default:
					$about_name = null;
			}
			$arr_reviewlist[$j]->about_name = $about_name;
		}

		return $arr_reviewlist;
	}
}