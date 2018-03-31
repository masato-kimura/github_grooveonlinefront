<?php
namespace album;

use Artist\domain\service\ArtistService;
use artist\model\dto\ArtistDto;
use Fuel\Core\Response;
use Review\domain\service\ReviewMusicService;
use Review\Model\Dto\ReviewMusicDto;
use Album\Domain\Service\AlbumService;
use Album\Model\Dto\AlbumDto;
use Fuel\Core\Pagination;

final class Controller_Album extends \Controller_Gol_Template
{
	public function before()
	{
		parent::before();
	}

	/**
	 *
	 * @param integer $id アルバムID
	 */
	public function action_detail($album_id=null)
	{
		\Log::debug('--------------------------------------');
		\Log::debug('[start]'. __METHOD__);

		try
		{
			// リクエストバリデーション
			AlbumService::validation_for_detail($album_id);

			// dtoにセット
			AlbumService::set_dto_for_detail($album_id);

			// アルバム情報を取得
			AlbumService::get_album_info();
			$album_dto = AlbumDto::get_instance();

			// アーティスト情報を取得
			$artist_dto = ArtistDto::get_instance();
			$artist_dto->set_artist_id($artist_dto->get_artist_id());
			ArtistService::get_artist_info();

			// レビューを取得
			ReviewMusicService::get_review_list();

			// レビューページネーション
			$review_dto = ReviewMusicDto::get_instance();
			$arr_request = array();
			$pagination_config = array(
				'pagination_url' => "/album/detail/{$album_id}/?". http_build_query($arr_request),
				'total_items'    => $review_dto->get_review_count(),
				'per_page'       => $review_dto->get_limit(),
				'uri_segment'    => 4,
			);

			$obj_view_model = \ViewModel::forge('album/detail', 'view', null, $this->device. '/album/detail');
			$obj_view_model->pagination = Pagination::forge('mypagination', $pagination_config);
			$this->template->content = $obj_view_model;
			$this->template->segment = 'album/detail/'. $album_id;
			$this->template->title = "{$album_dto->get_album_name()}/{$artist_dto->get_artist_name()} | グルーヴオンライン・アルバム";
			$this->template->page_name = "アルバム";
			$this->template->og_image = $album_dto->get_image_large();
			$this->template->og_description = mb_strimwidth('アルバム｜'. $album_dto->get_album_name(), 0, 50, '...');

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
