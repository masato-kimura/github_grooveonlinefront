<?php
use Fuel\Core\ViewModel;
use login\domain\service\LoginService;
use Review\domain\service\ReviewMusicService;
use \model\domain\service\InformationService;
use model\dto\InformationDto;

final class Controller_Index extends Controller_Gol_Template
{
	public function before()
	{
		parent::before();
	}

	public function action_index()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# ログイン情報をセッションから取得し、dtoにセット
			LoginService::set_user_info_to_dto_from_session();

			ReviewMusicService::get_top_contents();

			$this->template->content = ViewModel::forge('index/index', 'view', null, $this->device. '/index/index');
			$this->template->segment = 'index';
			$this->template->og_description = '大好きなアーティストのレビューをしよう！気になるあの曲も検索して視聴できる！';

			\Log::debug('[end]');
			\Log::debug('-----------------------------'. PHP_EOL);
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');
			$this->reflect_exception($e);
		}
	}


	public function action_info($page=1)
	{
		\Log::debug('--------------------------------------');
		\Log::debug('[start]'. __METHOD__);

		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# ログイン情報をセッションから取得し、dtoにセット
			LoginService::set_user_info_to_dto_from_session();

			# ページネーション
			$config = array(
				'pagination_url' => '/info/',
				'total_items'    => InformationService::get_information_count(), // countの場合はtrue
				'num_links'      => 4,
				'per_page'       => 10,
				'uri_segment'    => 2,
			);

			$pagination = Pagination::forge('mypagination', $config);

			# インフォメーション文言を取得
			InformationService::get_information_from_cache($pagination);

			# インフォメーション既読情報を取得
			InformationService::get_last_information_name();

			# インフォメーションの既読チェック
			$information_dto = InformationDto::get_instance();
			InformationService::set_read_information(array($information_dto->get_last_information_name()));

			$obj_view_model = ViewModel::forge('index/info', 'view', null, $this->device. '/index/info');
			$obj_view_model->set('pagination', $pagination);
			$this->template->content = $obj_view_model;
			$this->template->segment = 'index';
			$this->template->og_description = 'インフォメーション';

			\Log::debug('[end]');
			\Log::debug('-----------------------------'. PHP_EOL);
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');
			$this->reflect_exception($e);
		}

	}


	public function action_aboutus()
	{
		\Log::debug('--------------------------------------');
		\Log::debug('[start]'. __METHOD__);

		$this->template->content = ViewModel::forge('index/aboutus', 'view', null, $this->device. '/index/aboutus');
		$this->template->segment = 'aboutus';

		\Log::debug('[end]');
		\Log::debug('-----------------------------'. PHP_EOL);
	}


	public function action_test()
	{
		\Log::debug('--------------------------------------');
		\Log::debug('[start]'. __METHOD__);

		$this->template->content = ViewModel::forge('index/test', 'view', null, $this->device. '/index/test');
		$this->template->segment = 'test';

		\Log::debug('[end]');
		\Log::debug('-----------------------------'. PHP_EOL);
	}

}