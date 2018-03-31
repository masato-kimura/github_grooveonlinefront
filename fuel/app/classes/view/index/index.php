<?php
use login\domain\service\LoginService;
use login\model\dto\LoginDto;
use review\model\dto\ReviewMusicDto;
use Rank\model\dto\RankDto;
use Tracklist\Model\Dto\TracklistDto;
use user\domain\service\UserService;
class View_Index_Index extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto        = LoginDto::get_instance();
		$review_music_dto = ReviewMusicDto::get_instance();
		$tracklist_dto    = TracklistDto::get_instance();
		$rank_dto         = RankDto::get_instance();

		# オートログインの確認from session :: user_infoが存在するか
		$this->auto_login = 'no';
		if (LoginService::is_auto_login())
		{
			$this->auto_login = 'yes';
		}

		# セッションのユーザ情報をセット
		$this->arr_user_info_from_session = array();
		if ($user_id = $login_dto->get_user_id())
		{
			$login_hash = $login_dto->get_login_hash();
			$this->arr_user_info_from_session = array(
				'user_id'    => $login_dto->get_user_id(),
				'user_name'  => $login_dto->get_user_name(),
				'auth_type'  => $login_dto->get_auth_type(),
				'login_hash' => $login_dto->get_login_hash(),
				'is_first_regist' => $login_dto->get_is_first_regist(),
			);

			$hash6 = substr($login_hash, 0, 6);

			# プロフィール画像urlを取得
			$this->user_image = LoginService::get_image_profile_url($user_id, $login_hash, false);
		}

		# ユーザ情報
		$this->user_id   = $login_dto->get_user_id();
		$this->user_name = htmlentities($login_dto->get_user_name(), ENT_QUOTES, mb_internal_encoding());

		# 週刊ランキング
		$this->weekly_rank_track = $rank_dto->get_arr_track_weekly();
		$this->weekly_rank_album = $rank_dto->get_arr_album_weekly();
		$w = \Date::forge()->format('%w');
		if ($w == 0)
		{
			$diff = 13;
		}
		else
		{
			$diff = $w + 6;
		}
		// 一週間前の月曜日
		$this->weekly_rank_from = \Date::forge(time() - (60 * 60 * 24 * $diff))->format('%Y/%m/%d');
		$this->weekly_rank_to   = \Date::forge(time() - (60 * 60 * 24 * ($diff - 6)))->format('%Y/%m/%d');
		$this->weekly_rank_next = \Date::forge(time() - (60 * 60 * 24 * $diff) + (60 * 60 * 24 * 14))->format('%Y/%m/%d');

		# 最新トラックリスト
		$this->arr_tracklist = $this->_format_tracklist();

		# 最新レビュー
		$this->review_music = $review_music_dto->get_arr_list();

		# トップレビュー
		$this->top_review_music = $review_music_dto->get_arr_top_list();

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);

		return true;
	}

	private function _format_tracklist()
	{
		$tracklist_dto = TracklistDto::get_instance();
		$arr_tracklist = array();
		foreach ($tracklist_dto->get_arr_list() as $i => $val)
		{
			$arr_tracklist[$i] = $val;
			$arr_tracklist[$i]->is_tracks_and_more = false;
			$number = 1;
			foreach ($val->arr_tracks as $j => $track)
			{
				if ($number >= 4)
				{
					$arr_tracklist[$i]->is_tracks_and_more = true;
				}
				else
				{
					$val->arr_tracks_list[$j] = $track;
					$number++;
				}
			}
			if ( ! empty($val->user_id))
			{
				$arr_tracklist[$i]->user_name = $val->user_login_name;
				$arr_tracklist[$i]->user_image = UserService::get_user_image_url_small($val->user_id);
			}
			else
			{
				$arr_tracklist[$i]->user_name = $val->user_name;
				$arr_tracklist[$i]->user_image = null;
			}

		}
		return $arr_tracklist;
	}

}