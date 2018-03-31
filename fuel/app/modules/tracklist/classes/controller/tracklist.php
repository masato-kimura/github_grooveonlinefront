<?php
namespace tracklist;

use Tracklist\Domain\Service\TracklistService;
use Artist\Domain\Service\ArtistService;
use Artist\Model\Dto\ArtistDto;
use Tracklist\Model\Dto\TracklistDto;
final class Controller_Tracklist extends \Controller_Gol_Template
{
	public function before()
	{
		parent::before();
	}

	public function action_index($page=1)
	{
		\Log::debug('--------------------------------------');
		\Log::debug('[start]'. __METHOD__);

		try
		{
			// リクエストバリデーション
			TracklistService::validation_for_index($page);

			// dtoにセット
			TracklistService::set_dto_for_index($page);

			$obj_view_model = \ViewModel::forge('tracklist/index', 'view', null, $this->device. '/tracklist/index');
			$this->template->content = $obj_view_model;
			$this->template->segment = 'tracklist/index/';
			$this->template->title = "トラックリスト一覧 | グルーヴオンライン";
			$this->template->page_name = "トラックリスト一覧";
			$this->template->og_description = 'お気に入りのアーティストベストトラックリスト投稿の一覧';

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

			return \Response::forge($this->template);
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}

	public function action_artist($artist_id, $page=1)
	{
		\Log::debug('--------------------------------------');
		\Log::debug('[start]'. __METHOD__);

		try
		{
			// リクエストバリデーション
			TracklistService::validation_for_artist($artist_id, $page);

			// dtoにセット
			TracklistService::set_dto_for_artist($artist_id, $page);

			// アーティスト情報を取得
			ArtistService::get_artist_info();

			$artist_dto = ArtistDto::get_instance();

			$obj_view_model = \ViewModel::forge('tracklist/artist', 'view', null, $this->device. '/tracklist/artist');
			$this->template->content   = $obj_view_model;
			$this->template->segment   = 'tracklist/'. $artist_id;
			$this->template->title     = "トラックリスト / {$artist_dto->get_artist_name()} | グルーヴオンライン";
			$this->template->page_name = "トラックリスト一覧";
			$this->template->og_image  = $artist_dto->get_image_medium();
			$this->template->og_description = mb_strimwidth('トラックリスト｜'. $artist_dto->get_artist_name(), 0, 50, '...');

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

			return \Response::forge($this->template);
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	public function action_user($user_id, $track_list_id=null)
	{
		try
		{

		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	public function action_create($artist_id=null)
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// リクエストバリデーション
			TracklistService::validation_for_create($artist_id);

			// dtoにセット
			TracklistService::set_dto_for_create($artist_id);

			// アーティスト情報を取得
			ArtistService::get_artist_info();

			TracklistService::get_detail_from_api();

			$obj_view_model = \ViewModel::forge('tracklist/create', 'view', null, $this->device. '/tracklist/create');
			$this->template->content = $obj_view_model;
			$this->template->segment = 'tracklist/create/';
			$this->template->title = "トラックリスト作成！ | グルーヴオンライン";
			$this->template->page_name = "トラックリスト";
			$this->template->og_image = null;
			$this->template->og_description = 'トラックリスト作成！';
			$this->template->top_banner     = '';

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

			return \Response::forge($this->template);
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	public function action_detail($tracklist_id)
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// リクエストバリデーション
			TracklistService::validation_for_detail_display($tracklist_id);

			// dtoにセット
			TracklistService::set_dto_for_detail_display($tracklist_id);

			// トラックリスト情報を取得
			TracklistService::get_detail_from_api();

			$tracklist_dto = TracklistDto::get_instance();

			$obj_view_model = \ViewModel::forge('tracklist/detail', 'view', null, $this->device. '/tracklist/detail');
			$this->template->content = $obj_view_model;
			$this->template->segment = 'tracklist/detail';
			$this->template->title = $tracklist_dto->get_title(). " | グルーヴオンライン";
			if ($tracklist_dto->get_artist_name())
			{
				$this->template->title = $tracklist_dto->get_title(). " |　". $tracklist_dto->get_artist_name();
			}
			$this->template->page_name = "トラックリスト";
			$this->template->og_image = null;
			$this->template->og_description = $tracklist_dto->get_user_name(). 'さんのトラックリスト';
			$tx = '';
			if ( ! empty(current($tracklist_dto->get_arr_list())->track_artist_image_large))
			{
				$this->template->og_image = current($tracklist_dto->get_arr_list())->track_artist_image_large;
				$tx = array();
				$cnt = 0;
				foreach ($tracklist_dto->get_arr_list() as $i => $val)
				{
					if ($cnt > 3)
					{
						$tx[] = "more・・";
						break;
					}
					$tx[] = ($i + 1). ". ". $val->track_name;
					$cnt++;
				}
				$tx = implode(','. PHP_EOL, $tx);
			}
			$this->template->og_description = $tracklist_dto->get_user_name(). 'さんのトラックリスト/'. PHP_EOL. $tx. "　｜グルーヴオンライン";

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
