<?php
namespace track;

use Artist\domain\service\ArtistService;
use artist\model\dto\ArtistDto;
use Fuel\Core\Response;
use Review\domain\service\ReviewMusicService;
use Review\Model\Dto\ReviewMusicDto;
use Fuel\Core\Pagination;
use Track\Domain\Service\TrackService;
use Track\Model\Dto\TrackDto;

final class Controller_Track extends \Controller_Gol_Template
{
	public function before()
	{
		parent::before();
	}

	/**
	 *
	 * @param integer $id アルバムID
	 */
	public function action_detail($track_id=null)
	{
		\Log::debug('--------------------------------------');
		\Log::debug('[start]'. __METHOD__);

		try
		{
			// リクエストバリデーション
			TrackService::validation_for_detail($track_id);

			// dtoにセット
			TrackService::set_dto_for_detail($track_id);

			// トラック情報を取得
			TrackService::get_track_info();
			$track_dto  = TrackDto::get_instance();

			// アーティスト情報を取得
			$artist_dto = ArtistDto::get_instance();
			$artist_dto->set_artist_id($track_dto->get_artist_id());
			ArtistService::get_artist_info();

			// レビューを取得
			ReviewMusicService::get_review_list();

			// レビューページネーション
			$arr_request = array();
			$review_dto = ReviewMusicDto::get_instance();
			$pagination_config = array(
					'pagination_url' => "/track/detail/{$track_id}/?". http_build_query($arr_request),
					'total_items'    => $review_dto->get_review_count(),
					'per_page'       => $review_dto->get_page(),
					'uri_segment'    => 4,
			);

			$obj_view_model = \ViewModel::forge('track/detail', 'view', null, $this->device. '/track/detail');
			$obj_view_model->pagination = Pagination::forge('mypagination', $pagination_config);
			$this->template->content = $obj_view_model;
			$this->template->segment = 'track/detail/'. $track_id;
			$this->template->title = "{$track_dto->get_track_name()}/{$track_dto->get_artist_name()} | グルーヴオンライン";
			$this->template->page_name = "トラック";
			$this->template->og_image = $track_dto->get_image_large();
			$this->template->og_description = mb_strimwidth('トラック｜'. $track_dto->get_track_name(), 0, 50, '...');

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

			return \Response::forge($this->template);
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}
}
