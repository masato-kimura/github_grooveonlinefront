<?php
namespace Review;

use Login\Model\Dto\LoginDto;
use Artist\Model\Dto\ArtistDto;
use Review\Model\Dto\ReviewMusicDto;
use Artist\Domain\Service\ArtistService;
use Album\Domain\Service\AlbumService;
use Track\Domain\Service\TrackService;
use Review\Domain\Service\ReviewMusicService;
use user\domain\service\UserService;
use user\model\dto\UserDto;
use Album\Model\Dto\AlbumDto;
use Track\Model\Dto\TrackDto;
use login\domain\service\LoginService;

final class Controller_Music extends \Controller_Gol_Template
{

	public function before()
	{
		parent::before();
	}

	public function action_index($artist_id=null)
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			\Log::info($artist_id);

			# リクエストバリデーション
			ReviewMusicService::validation_for_index($artist_id);

			# DTOにセット
			ReviewMusicService::set_dto_for_index($artist_id);

			$obj_view_model = \ViewModel::forge('music/index', 'view', null, $this->device. '/music/index');
			$this->template->content = $obj_view_model;
			$this->template->segment = 'review/music/index';
			$this->template->title = "音楽レビュー一覧 | グルーヴオンライン";
			$this->template->page_name = "音楽レビュー";
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}

	public function action_artist($artist_id=null)
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			\Log::info($artist_id);

			# リクエストバリデーション
			ReviewMusicService::validation_for_artist($artist_id);

			# DTOにセット
			ReviewMusicService::set_dto_for_artist($artist_id);

			# APIからレビューを取得しDTOにセット
			ArtistService::get_artist_info();

			$obj_view_model = \ViewModel::forge('music/artist', 'view', null, $this->device. '/music/artist');
			$this->template->content = $obj_view_model;
			$this->template->segment = 'review/music/index';
			$this->template->title = "音楽レビュー一覧 | グルーヴオンライン";
			$this->template->page_name = "音楽レビュー";
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	/**
	 * グルーヴオンライン・ミュージックレビュー投稿画面
	 * 事前条件：ログインしていること
	 */
	public function action_write($artist_id)
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストバリデーション
			ReviewMusicService::validation_for_write($artist_id);

			# ログイン状態によって挙動を変える
			$login_dto = LoginDto::get_instance();
			$user_id = $login_dto->get_user_id();
			if (empty($user_id))
			{
				$obj_session_from = new \stdClass();
				$obj_session_from->url = "review/music/write/{$artist_id}/";
				$obj_session_from->timestamp = \Date::forge()->get_timestamp();
				\Session::set('from', $obj_session_from);
			}

			# ログイン情報をセッションから取得し、dtoにセット
			LoginService::set_user_info_to_dto_from_session();

			ReviewMusicService::set_dto_for_write_from_session($artist_id);
			ReviewMusicService::set_dto_for_write($artist_id);

			# マスタ情報取得
			ArtistService::get_artist_info();
			AlbumService::get_album_info_only_tmp_review();
			TrackService::get_track_info_only_tmp_review();

			#アーティストレビュー取得
			ReviewMusicService::get_artist_review_one();

			$artist_dto = ArtistDto::get_instance();

			$obj_view_model = \ViewModel::forge('music/write', 'view', null, $this->device. '/music/write');
			$obj_view_model->set('arr_error', array());
			$this->template->content = $obj_view_model;
			$this->template->segment = 'review/music/write';
			$this->template->top_banner = '';
			$this->template->title = $artist_dto->get_artist_name(). "レビュー投稿ページ | グルーヴオンライン・";

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

			return \Response::forge($this->template);
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	public function action_detail($about, $id)
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストバリデーション
			ReviewMusicService::validation_for_detail($about, $id);

			# DTOにセット
			ReviewMusicService::set_dto_for_detail($about, $id);

			# レビュー情報を取得
			ReviewMusicService::get_review_detail();

			$review_dto = ReviewMusicDto::get_instance();
			$arr_review = $review_dto->get_arr_list();
			$arr_review = array_filter($arr_review);
			if (empty($arr_review))
			{
				throw new \Exception('review not found', 404);
			}

			# クールユーザ情報を取得
			ReviewMusicService::get_cool_users();

			# アーティスト情報を取得
			$artist_dto = ArtistDto::get_instance();
			$artist_dto->set_artist_id(current($arr_review)->artist_id);
			ArtistService::get_artist_info();
			$to_title = $artist_dto->get_artist_name();

			switch ($about)
			{
				case 'album':
					# アルバム情報を取得
					$album_dto = AlbumDto::get_instance();
					$album_dto->set_album_id(current($arr_review)->album_id);
					AlbumService::get_album_info();
					$to_title .= "/{$album_dto->get_album_name()} アルバム";
					break;
				case 'track':
					# トラック情報を取得
					$track_dto = TrackDto::get_instance();
					$track_dto->set_track_id(current($arr_review)->track_id);
					TrackService::get_track_info();
					$to_title .="/{$track_dto->get_track_name()} トラック";
					break;
				default:
					$to_title .= " アーティスト";
			}

			# ユーザ情報を取得
			$user_dto = UserDto::get_instance();
			$user_dto->set_user_id(current($arr_review)->user_id);
			$user_dto->set_disp_user_id(current($arr_review)->user_id);
			UserService::get_user_info();

			$obj_view_model = \ViewModel::forge('music/detail', 'view', null, $this->device. '/music/detail');
			$obj_view_model->set('arr_error', array());
			$this->template->content = $obj_view_model;
			$this->template->segment = 'review/music/detail/'. $about. '/'. $id;
			$this->template->title = $user_dto->get_user_name()."さんの". mb_strimwidth($to_title, 0, 50, '...'). "レビュー | グルーヴオンライン";
			$this->template->page_name = "音楽レビュー";
			$this->template->og_type = "article";
			$this->template->og_image = current($review_dto->get_arr_list())->image_large;
			$this->template->og_description = mb_strimwidth(current($review_dto->get_arr_list())->review, 0, 50, '...');

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
