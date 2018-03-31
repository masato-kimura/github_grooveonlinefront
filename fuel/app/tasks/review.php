<?php
namespace Fuel\Tasks;

use Review\Model\Dto\ReviewMusicDto;
use Review\domain\service\ReviewMusicService;
use Fuel\Core\Cache;
class Review
{
	public static function getnew()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_music_dto = ReviewMusicDto::get_instance();

		# 最新レビュー
		$review_music_dto->set_limit(7);
		$review_music_dto->set_about('all');
		ReviewMusicService::get_review_list();

		$arr_result = $review_music_dto->get_arr_list();

		Cache::set('review_getnew', $arr_result);

		return true;
	}


	public static function gettop()
	{
		\Log::debug('[start]'. __METHOD__);

		$review_music_dto = ReviewMusicDto::get_instance();

		# トップレビュー
		$review_music_dto->set_top_limit(3);
		$review_music_dto->set_top_about('all');
		ReviewMusicService::get_top_review();

		$arr_result = $review_music_dto->get_arr_top_list();

		Cache::set('review_gettop', $arr_result);

		return true;
	}
}