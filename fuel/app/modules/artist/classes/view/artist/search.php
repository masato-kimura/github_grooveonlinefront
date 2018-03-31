<?php
use artist\model\dto\ArtistDto;
use util\Display;
class View_Artist_Search extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$this->artist_name    = $artist_dto->get_artist_name();
		$this->loading_top    = Display::loading('loading_top');
		$this->loading_bottom = Display::loading('loading_bottom');

		$this->last_search_more_flg = false;
		$this->arr_last_search = $this->_get_last_search();
	}

	private function _get_last_search()
	{
		$arr_list = \Cache::get('artist_search_new', array());
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