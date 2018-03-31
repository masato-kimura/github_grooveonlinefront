<?php
use Fuel\Core\Agent;
use login\domain\service\LoginService;
use login\model\dto\LoginDto;
use user\model\dto\UserDto;
use Fuel\Core\Uri;
use Fuel\Core\Response;
use model\domain\service\InformationService;
use model\dto\UserInformationDto;
use Fuel\Core\Request;

class Controller_Gol_Template extends \Controller_Template
{
	// 画面表示デバイス
	private $timestamp_expired = 0;
	protected $device = "pc";
	public $template = "template";

	public function before()
	{
		\Log::debug('[start]'. __METHOD__);
		\Log::info(Request::active()->action);

		if (Request::active()->action === 'undefined')
		{
			\Log::debug('exit cause undefined'. PHP_EOL. PHP_EOL);
			exit;
		}

		\Log::info(\Session::get());

		// セッション値の基本有効期間 (timestamp)
		$this->timestamp_expired = 60 * 60 * 24 * 100; // 100day max 2yearらしい

		// デバイス情報をメンバ変数にセット
		$this->set_device();

		// デバイスに合わせてテンプレートをセット
		$this->set_template();

		parent::before();

		# セッション情報をDTOにセット(LoginDto, UserDto)
		LoginService::set_user_info_to_dto_from_session();

		$login_dto  = LoginDto::get_instance();
		$auto_login = $login_dto->get_auto_login();
		if (empty($auto_login))
		{
			# セッションに存在する自動ログインフラグがfalseの場合はブラウザcloseと同時にログアウト
			\Session::instance()->set_config('expire_on_close', true);
		}

		// ページ毎のセッションの有効期間をチェックし過ぎていたらセッション削除
		Loginservice::check_session_expired($this->timestamp_expired);

		// 運営からのインフォメーションの読み込みチェック
		$last_information_name = \Cache::get('last_information_name');
		InformationService::set_unread_information(array($last_information_name));
		$unread_information_count = InformationService::get_unread_information_count();

		// ユーザー毎のインフォメーションを取得（15分毎）
		InformationService::get_user_information_from_api();
		$user_information_dto = UserInformationDto::get_instance();
		$unread_user_information_count = $user_information_dto->get_count();

		$this->template->user_id     = $login_dto->get_user_id();
		$this->template->user_name   = htmlentities($login_dto->get_user_name(), ENT_QUOTES, mb_internal_encoding());
		$this->template->title       = "音楽でつながれる世界がある｜グルーヴオンライン";
		$this->template->set_global('real_device', $this->device);
		$this->template->segments    = implode('/', $this->request->route->segments);
		$this->template->top_banner  = $this->_make_pr_top_banner();
		$this->template->unread_information_count      = empty($unread_information_count)? '&nbsp;': $unread_information_count;
		$this->template->unread_user_information_count = empty($unread_user_information_count)? '&nbsp;': $unread_user_information_count;
		$this->response = Response::forge();
		$this->response->set_header('X-FRAME-OPTIONS', 'SAMEORIGIN');
	}

	public function action_undefined()
	{
		\Log::debug('[start]'. __METHOD__);
		exit;
	}


	public function after($response)
	{
		$response = $this->response;
		$response->body = $this->template;
		return parent::after($response);
	}

	private function _make_pr_top_banner()
	{
		$banner = null;
		if ( ! \Agent::is_smartphone())
		{
			$arr_banner = array();
			$arr_banner[0] = "<div id='ibb-widget-root'></div><script>(function(t,e,i,d){var o=t.getElementById(i),n=t.createElement(e);o.style.height=90;o.style.width=728;o.style.display='inline-block';n.id='ibb-widget',n.setAttribute('src',('https:'===t.location.protocol?'https://':'http://')+d),n.setAttribute('width','728'),n.setAttribute('height','90'),n.setAttribute('frameborder','0'),n.setAttribute('scrolling','no'),o.appendChild(n)})(document,'iframe','ibb-widget-root','banners.itunes.apple.com/banner.html?partnerId=&aId=". \Config::get('itunes.affiliate_id'). "&bt=promotional&at=Music&st=apple_music&c=jp&l=ja-JP&w=728&h=90&rs=1');</script>";
			$arr_banner[1] = "<iframe src='//banners.itunes.apple.com/banner.html?partnerId=&aId=&bt=genre&t=genre_matrix_black&ft=topalbums&st=music&s=34&p=11&c=jp&l=ja-JP&w=728&h=90' frameborder=0 style='overflow-x:hidden;overflow-y:hidden;width:728px;height:90px;border:0px'></iframe>";
			$arr_banner[2] = "<iframe src='//banners.itunes.apple.com/banner.html?partnerId=&aId=&bt=genre&t=genre_matrix_black&ft=topsongs&st=music&s=34&p=1&c=jp&l=ja-JP&w=728&h=90' frameborder=0 style='overflow-x:hidden;overflow-y:hidden;width:728px;height:90px;border:0px'></iframe>";
			$arr_banner[3] = "<iframe src='//banners.itunes.apple.com/banner.html?partnerId=&aId=&bt=promotional&at=Music&st=apple_music_family&c=jp&l=ja-JP&w=728&h=90&rs=1' frameborder=0 style='overflow-x:hidden;overflow-y:hidden;width:728px;height:90px;border:0px'></iframe>";
			$arr_banner[4] = '<iframe src="http://rcm-fe.amazon-adsystem.com/e/cm?t=grooveonlin0c-22&o=9&p=293&l=ur1&category=primemusic&banner=042JW7PEJA0XC5PTAY82&f=ifr" width="640" height="100" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';
			$arr_banner[5] = '<iframe src="http://rcm-fe.amazon-adsystem.com/e/cm?t=grooveonlin0c-22&o=9&p=48&l=ur1&category=music&f=ifr" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';
			$arr_banner[6] = '<iframe src="http://rcm-fe.amazon-adsystem.com/e/cm?t=grooveonlin0c-22&o=9&p=48&l=ur1&category=mi_guitar&banner=04WMEKN3VDMDJB91VH02&f=ifr" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';

			switch (current($this->request->route->segments))
			{
				case 'index':
					$banner = $arr_banner[0];
					break;
				case 'album':
					$banner = $arr_banner[1];
					break;
				case 'track':
					$banner = $arr_banner[2];
					break;
				default:
					$i = rand(0,6);
					$banner = $arr_banner[$i];
			}
		}
		else
		{
			$ua = \Input::server('http_user_agent', '');
			if ( ((strpos($ua, 'iPhone') !== false) || (strpos($ua, 'iPod') !== false) || (strpos($ua, 'iPad') !== false)) )
			{
				$arr_banner = array();
				$arr_banner[0] = "<iframe src='//banners.itunes.apple.com/banner.html?partnerId=&aId=". \Config::get('itunes.affiliate_id'). "&bt=promotional&at=Music&st=apple_music&c=jp&l=ja-JP&w=320&h=100&rs=1' frameborder=0 style='overflow-x:hidden;overflow-y:hidden;width:320px;height:100px;border:0px'></iframe>";
				$arr_banner[1] = "<iframe src='//banners.itunes.apple.com/banner.html?partnerId=&aId=". \Config::get('itunes.affiliate_id'). "&bt=promotional&at=Music&st=apple_music_family&c=jp&l=ja-JP&w=320&h=50&rs=1' frameborder=0 style='overflow-x:hidden;overflow-y:hidden;width:320px;height:50px;border:0px'></iframe>";
				$arr_banner[2] = "<iframe src='//banners.itunes.apple.com/banner.html?partnerId=&aId=". \Config::get('itunes.affiliate_id'). "&bt=promotional&at=Music&st=apple_music&c=jp&l=ja-JP&w=320&h=100&rs=1' frameborder=0 style='overflow-x:hidden;overflow-y:hidden;width:320px;height:100px;border:0px'></iframe>";
				$i = rand(0, 2);
				$banner = $arr_banner[$i];
			}
			else
			{
				$arr_banner = array();
				$arr_banner[0] = '<iframe src="http://rcm-fe.amazon-adsystem.com/e/cm?t=grooveonlin0c-22&o=9&p=294&l=ur1&category=primemusic&banner=00X063EWZB25BABS9NG2&f=ifr" width="320" height="100" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';
				$arr_banner[1] = '<iframe src="http://rcm-fe.amazon-adsystem.com/e/cm?t=grooveonlin0c-22&o=9&p=288&l=ur1&category=mi_guitar&banner=0H7NB5GN2ZDBN5W14982&f=ifr" width="320" height="50" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';
				$arr_banner[2] = "<iframe src='//banners.itunes.apple.com/banner.html?partnerId=&aId=". \Config::get('itunes.affiliate_id'). "&bt=promotional&at=Music&st=apple_music&c=jp&l=ja-JP&w=320&h=100&rs=1' frameborder=0 style='overflow-x:hidden;overflow-y:hidden;width:320px;height:100px;border:0px'></iframe>";
				$i = rand(0, 2);
				$banner = $arr_banner[$i];
			}
		}

		return $banner;
	}


	/**
	 *
	 * @param unknown $device
	 * @param string $controller
	 * @param string $method
	 */
	public function action_device($device, $controller=null, $method=null, $param1=null, $param2=null, $param3=null)
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			switch ($device)
			{
				case 'pc':
					\Session::set('device', 'pc');
					break;
				case 'smartphone':
					\Session::set('device', 'smartphone');
					break;
			}

			\Log::debug('[end]');
			\Log::debug('-----------------------------'. PHP_EOL);

			$url = $controller;
			if ( ! empty($method)) $url .= '/'. $method;
			if ( ! empty($param1)) $url .= '/'. $param1;
			if ( ! empty($param2)) $url .= '/'. $param2;
			if ( ! empty($param3)) $url .= '/'. $param3;

			return Response::redirect(Uri::create($url));

		}
		catch (\Exception $e)
		{
			\Log::info($e->getMessage());
			\Log::info($e->getFile(). '['. $e->getLine(). ']');
			$this->reflect_exception($e);
		}
	}


	/**
	 * 例外処理を受けて画面表示させる
	 * @param Exception $e
	 */
	protected function reflect_exception($e, $redirect_url=null)
	{
		\Log::debug('[start]'. __METHOD__);

		\Log::error($e->getMessage(). '['. $e->getCode(). ']');
		\Log::error($e->getFile(). '['. $e->getLine(). ']');

		$re_redirect_url = empty($redirect_url)?  \Config::get('host.base_url_http'): $redirect_url;

		switch ($this->device)
		{
			case 'pc':
				$device = 'pc';
				break;
			case 'smartphone':
			case 'sp':
				$device = 'smartphone';
				break;
			default:
				$device = 'smartphone';
		}

		switch ($e->getCode())
		{
			case '404': // 404 page not foundを表示させたい場合
				$obj_view_model = ViewModel::forge('err/404', 'view', null, $this->device. '/err/404');
				$obj_view_model->set('e', $e);
				$this->template->content = $obj_view_model;
				break;
			case '7010'	: // ログインエラー
				\Session::delete('user_info');
				\Session::delete('favorite_users');
				\Response::redirect($re_redirect_url);
				break;
			case '7012': // ログイン済でログイン画面へのアクセス時
				\Response::redirect($re_redirect_url);
				break;
			default:
				if (isset($redirect_url))
				{
					\Response::redirect($redirect_url);
				}

				$obj_view_model = ViewModel::forge('err/index', 'view', null, $this->device. '/err/index');
				$obj_view_model->set('e', $e);
				$this->template->content = $obj_view_model;

		}
	}


	private function set_device()
	{
		\Log::debug('[start]'. __METHOD__);

		if (Agent::is_mobiledevice())
		{
			$real_device = 'smartphone';
		}
		else if (\Agent::is_smartphone())
		{
			$real_device = 'smartphone';
		}
		else
		{
			$real_device = "smartphone";
		}

		// セッションを優先
		switch (\Session::get('device'))
		{
			case 'pc':
				$this->device = 'pc';
				break;
			case 'smartphone':
				$this->device = 'smartphone';
				break;
			default:
				$this->device = $real_device;
		}

		return true;
	}


	private function set_template()
	{
		\Log::debug('[start]'. __METHOD__);

		switch($this->device)
		{
			case "mobile":
				$this->template = "template_mobile";
				break;
			case "smartphone":
				$this->template = "template_smartphone";
				break;
			case "pc":
				$this->template = "template_pc";
				break;
			default :
				$this->template = "template_smartphone";
		}

		return true;
	}
}
