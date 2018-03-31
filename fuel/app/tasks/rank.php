<?php
namespace Fuel\Tasks;

use Rank\Model\Dto\RankDto;
use Rank\domain\service\RankService;
use Fuel\Core\Cache;
class Rank
{
	/**
	 *
	 * @param string $about (track|album)
	 * @return boolean
	 */
	public static function week($about='track')
	{
		\Log::debug('[start]'. __METHOD__);

		$rank_dto = RankDto::get_instance();

		# 最新レビュー
		$rank_dto->set_offset(0);
		$rank_dto->set_limit(100);
		$rank_dto->set_about($about);
		RankService::get_rank_weekly();

		$arr_result = array();
		foreach ($rank_dto->get_arr_list() as $val)
		{
			if ($val->rank > 100) break;
			$arr_result[] = $val;
		}

		switch ($about)
		{
			case 'track':
				Cache::set('rank_track_weekly', $arr_result);
				break;
			case 'album':
				Cache::set('rank_album_weekly', $arr_result);
				break;
		}

		return true;
	}
}