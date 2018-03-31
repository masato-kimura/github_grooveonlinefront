<?php
namespace User;

use user\domain\service\UserService;
use Review\domain\service\ReviewMusicService;
use Fuel\Core\Pagination;
use user\model\dto\UserDto;
use Review\Model\Dto\ReviewMusicDto;
use login\domain\service\LoginService;
final class Controller_User extends \Controller_Gol_Template
{
	public function before()
	{
		parent::before();
	}

	/**
	 * ユーザページ
	 * 表示ユーザIDはuser_dtoのdisp_user_idに格納
	 * ログインユーザIDはuser_dtoのuser_idに格納
	 * @param string $user_id
	 */
	public function action_you($user_id=NULL, $page=0)
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// バリデーション
			UserService::validation_for_you($user_id);

			// リクエスト情報をDtoに格納
			UserService::set_dto_for_you($user_id);

			// ユーザ情報を取得
			UserService::get_user_info();

			// お気に入りユーザ情報を取得
			UserService::get_favorite_users($user_id);

			// レビュー一覧を取得
			//ReviewMusicService::get_all_review();

			// セッションからユーザインフォメーション情報を削除
			UserService::unset_user_information();

			// ページネーション
			$arr_request = array();
			$review_music_dto = ReviewMusicDto::get_instance();
			$pagination_config = array(
				'pagination_url' => '/user/you/'. $user_id. '/?user_review=1&'. http_build_query($arr_request),
				'total_items'    => $review_music_dto->get_review_count(),
				'per_page'       => $review_music_dto->get_limit(),
				'uri_segment'    => 4,
			);

			$user_dto = UserDto::get_instance();
			$obj_view_model = \ViewModel::forge('user/you', 'view', null, $this->device. '/user/you');

			$obj_view_model->pagination = Pagination::forge('mypagination', $pagination_config);
			$this->template->content        = $obj_view_model;
			$this->template->segment        = 'user/you/'. $user_id;
			$this->template->title          = $user_dto->get_user_name(). "さんのプロフィールページ ｜ グルーヴオンライン";
			$this->template->page_name      = 'ユーザーページ';
			$this->template->og_type        = 'article';
			$this->template->og_image       = LoginService::get_user_image_url_extralarge($user_id);
			$this->template->og_description = mb_strimwidth($user_dto->get_profile_fields(), 0, 50, '...');
			$this->template->top_banner     = '';

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
}