<?php
namespace Fuel\Tasks;

use Fuel\Core\Cache;
use Artist\Model\Dto\ArtistDto;
use Api\domain\service\ArtistService;
class Search
{
	/**
	 * batch
	 * @return boolean
	 */
	public static function artist_new()
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();

		# 最近検索されたアーティスト
		$artist_dto->set_offset(0);
		$artist_dto->set_limit(100);

		ArtistService::get_artist_search('new');

		Cache::set('artist_search_new', $artist_dto->get_arr_list());

		return true;
	}
}