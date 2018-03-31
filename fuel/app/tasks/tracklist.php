<?php
namespace Fuel\Tasks;

use Tracklist\Model\Dto\TracklistDto;
use Tracklist\Domain\Service\TracklistService;
class Tracklist
{
	public static function getnew()
	{
		\Log::debug('[start]'. __METHOD__);

		$tracklist_dto = TracklistDto::get_instance();

		# 最新レビュー
		$tracklist_dto->set_limit(5);
		$tracklist_dto->set_offset(0);

		# 取得
		TracklistService::get_list_from_api();

		# キャッシュにセット
		\Cache::set('tracklist_getnew', $tracklist_dto->get_arr_list());

		return true;
	}
}