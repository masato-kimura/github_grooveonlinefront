<?php
namespace artist;

use Fuel\Core\Response;
use Artist\domain\service\ArtistService;
use Album\Domain\Service\AlbumService;
use Review\domain\service\ReviewMusicService;
use Artist\Model\Dto\ArtistDto;
use Review\Model\Dto\ReviewMusicDto;

final class Controller_Artist extends \Controller_Gol_Template
{
	public function before()
	{
		parent::before();
	}

	/**
	 *
	 * @param integer $id アーティストID
	 */
	public function action_detail($artist_id=null)
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// バリデーション
			ArtistService::validation_for_detail($artist_id);

			// リクエストをDTOにセット
			ArtistService::set_dto_for_detail($artist_id);

			// アーティスト情報を取得
			ArtistService::get_artist_info();

			// アルバム情報を取得
			AlbumService::get_album_list();

			// レビューを取得
			ReviewMusicService::get_review_list();

			// レビュー一覧ページネーション
			$review_dto = ReviewMusicDto::get_instance();
			$arr_request = array();
			$pagination_config = array(
				'pagination_url' => "/artist/detail/{$artist_id}/?" . http_build_query($arr_request),
				'total_items'    => $review_dto->get_review_count(),
				'per_page'       => $review_dto->get_limit(),
				'uri_segment'    => 4,
			);

			$artist_dto = ArtistDto::get_instance();
			$obj_view_model = \ViewModel::forge('artist/detail', 'view', null, $this->device. '/artist/detail');
			$obj_view_model->pagination = \Pagination::forge('mypagination', $pagination_config);
			$this->template->title          = $artist_dto->get_artist_name(). " アーティスト | グルーヴオンライン";
			$this->template->page_name      = "アーティスト";
			$this->template->content        = $obj_view_model;
			$this->template->segment        = 'artist/detail/'. $artist_id;
			$this->template->og_image       = $artist_dto->get_image_large();
			$this->template->og_description = $artist_dto->get_artist_name(). " アーティストページ";

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			$this->reflect_exception($e);
		}
	}


	public function action_search($to=null)
	{
		\Log::debug('--------------------------------------');
		\Log::debug('[start]'. __METHOD__);

		try
		{
			// バリデーション
			ArtistService::validation_for_search($to);

			// リクエストをDTOにセット
			ArtistService::set_dto_for_search($to);

			$obj_view_model = \ViewModel::forge('artist/search', 'view', null, $this->device. '/artist/search');
			$obj_view_model->set('to', $to);
			$obj_view_model->set('arr_error', array());
			$this->template->content = $obj_view_model;
			$this->template->segment = 'artist/search';
			$this->template->title = "アーティストを検索して視聴やレビューをしよう！ | グルーヴオンライン";
			$this->template->page_name = "アーティスト検索";

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	/**
	 * 現状アーティストの登録はここで行わずリダイレクトのみです
	 * @param string $to
	 * @throws \Exception
	 */
	public function action_regist($to=null)
	{
		\Log::debug('--------------------------------------');
		\Log::debug('[start]'. __METHOD__);

		try
		{
			# リクエストバリデーション
			ArtistService::validation_for_regist($to);

			# リクエストをDTOにセット
			ArtistService::set_dto_for_regist($to);

			# リダイレクト
			$artist_dto = ArtistDto::get_instance();
			switch ($to)
			{
				case 'review':
					\Log::debug("リダイレクトします: /review/music/write/". $artist_dto->get_artist_id(). "/");
					\Log::debug('[end]');
					\Log::debug('--------------------------------------'. PHP_EOL);
					\Response::redirect("/review/music/write/". $artist_dto->get_artist_id(). "/");
					break;
				default:
					\Log::debug("リダイレクトします: /artist/detail/". $artist_dto->get_artist_id(). "/");
					\Log::debug('[end]');
					\Log::debug('--------------------------------------'. PHP_EOL);
					\Response::redirect("/artist/detail/". $artist_dto->get_artist_id(). "/");
			}
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}

}
