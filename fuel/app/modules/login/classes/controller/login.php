<?php
namespace Login;

use util;
use login\domain\service\LoginService;
use login\domain\service\RegistService;
use login\domain\service\EmailService;
use login\model\dto\LoginDto;
use user\model\dto\UserDto;

use login\domain\service\OauthLoginFactory;
use Fuel\Core\Response;
use Fuel\Core\ViewModel;

final class Controller_Login extends \Controller_Gol_Template
{
	public function before()
	{
		parent::before();
	}

	/**
	 * グルーヴオンライン・ログイン選択画面
	 * 事前条件：ログインしていないこと
	 */
	public function action_index()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// バリデーション
			LoginService::validation_for_index();

			$this->template->title = "グルーヴオンライン・ログイン選択画面";
			$this->template->content = \ViewModel::forge('login/index', 'view', null, $this->device. '/login/index');
			$this->template->segment = 'login/index';

			if ( ! preg_match('/review\/music/i', \Input::referrer()))
			{
				\Session::delete('from');
				\Session::delete('tmp_review');
				\Session::delete('search_artist');
			}

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	/**
	 * グルーヴオンライン・ログアウトコントローラー
	 * 事前条件：ログインしていること
	 */
	public function action_logout()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// バリデーション
			LoginService::validation_for_logout();

			# apiにログアウト情報を送信
			LoginService::logout(true);

			# セッションおよびクッキーのログイン情報を削除
			LoginService::session_destroy();

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

			# ログアウト後の遷移画面
			\Response::redirect(\Config::get('host.base_url_http'));
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e, \Config::get('host.base_url_http'));
		}
	}


	/**
	 * グルーヴオンラインのオリジナルアカウントを新規作成します。
	 * 事前条件：ログインしていないこと
	 * ほぼ戻ってきた場合の処理です
	 */
	public function action_grooveonlineregistindex()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// バリデーション
			LoginService::validation_for_grooveonlineregistindex();

			# 招待されたユーザの場合のリクエストパラメータをセッションへ格納
			$parameter = LoginService::make_parameter();

			//----------------------------
			// viewのセット
			//----------------------------
			$obj_view_model = \ViewModel::forge('login/grooveonlineregistindex', 'view', null, $this->device. '/login/grooveonlineregistindex');
			$obj_view_model->set('arr_error', array());
			$obj_view_model->set('parameter', $parameter);
			$this->template->title = "グルーヴオンライン・新規アカウント作成";
			$this->template->content = $obj_view_model;
			$this->template->segment = 'login/grooveonlineregistindex';

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

			return \Response::forge($this->template);
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	/**
	 * グルーヴオンラインのオリジナルアカウントの新規登録確認画面
	 * 事前条件：ログインしていないこと
	 * @throws \Exception
	 */
	public function action_grooveonlineregistconfirm()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// リクエストバリデーションチェック
			LoginService::validation_for_grooveonlineregistconfirm();
			RegistService::validation_for_grooveonlineregistconfirm();
			$arr_error = RegistService::get_arr_error();

			# 招待されたユーザの場合のリクエストパラメータをセッションへ格納
			$parameter = LoginService::make_parameter();

			if ( ! empty($arr_error))
			{
				$obj_view_model = \ViewModel::forge('login/grooveonlineregistindex', 'view', null, $this->device. '/login/grooveonlineregistindex');
				$obj_view_model->set('parameter', $parameter);
				$obj_view_model->set('arr_error', $arr_error);
				$this->template->title = "グルーヴオンライン・新規登録確認画面";
				$this->template->content = $obj_view_model;
				$this->template->segment = 'login/grooveonlineregistindex';

				return Response::forge($this->template);
			}

			// アップロード画像処理
			$obj_image = new util\Image();
			$prefix_unique_name = md5(__CLASS__. __FUNCTION__.
					\Input::post('email', 'none'). \Input::post('auth_type', 'none'));

			if ( ! empty($_FILES['pic']['name']))
			{
				# アップロードされた画像を取得し/assets/img/tmp/ディレクトリに保存する。その元画像情報をメンバ変数にセット
				if ( ! $obj_image->get_uploaded_tmp_image(null, $prefix_unique_name))
				{
					switch ($obj_image->get_error())
					{
						case \Upload::UPLOAD_ERR_MAX_SIZE:
						case \Upload::UPLOAD_ERR_INI_SIZE:
						case \Upload::UPLOAD_ERR_FORM_SIZE:
							$arr_error['image'] = '画像アップロードに失敗しました。画像サイズが5Mバイトを超えてます';
							break;
						default:
							$arr_error['image'] = '画像アップロードに失敗しました';
					}

					$obj_view_model = \ViewModel::forge('login/grooveonlineregistindex', 'view', null, $this->device. '/login/grooveonlineregistindex');
					$obj_view_model->set('parameter', $parameter);
					$obj_view_model->set('arr_error', $arr_error);
					$this->template->title = "グルーヴオンライン・新規登録確認画面";
					$this->template->content = $obj_view_model;
					$this->template->segment = 'login/grooveonlineregistindex';

					return Response::forge($this->template);
				}


				$arr_uploaded_image_info = $obj_image->get_uploaded_image_info();
				$modify_dir_path = preg_replace('/\/[^\/]+$/', '/', $arr_uploaded_image_info['path']);
				$modify_file_path = $modify_dir_path. \Date::forge()->format('%Y%m%d_'). $prefix_unique_name. '.jpg';

				# tmpディレクトリにアップロードされたファイルをリサイズし再配置
				$obj_image->modify_image($arr_uploaded_image_info['path'], $modify_file_path, 252, 252);

				# 生成された画像ファイル名をセッションに格納
				preg_match('/^.+\/(.+$)/', $modify_file_path, $match);
				$obj_to_session = new \stdClass();
				$obj_to_session->name = $match[1];
				$obj_to_session->path = $modify_file_path;
				$obj_to_session->timestamp = \Date::forge()->get_timestamp();
				\Session::set('tmp_image', $obj_to_session);
			}

			$obj_view_model = \ViewModel::forge('login/grooveonlineregistconfirm', 'view', null, $this->device. '/login/grooveonlineregistconfirm');
			$obj_view_model->set('parameter', $parameter);
			$this->template->title = "グルーヴオンライン・ユーザー登録確認画面";
			$this->template->content = $obj_view_model;
			$this->template->segment = 'login/grooveonlineregistconfirm';

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

			return Response::forge($this->template);


		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	/**
	 * グルーヴオンラインのオリジナルアカウント新規登録実行画面
	 * 事前条件：ログインしていないこと
	 * @throws \Exception
	 */
	public function action_grooveonlineregistexecute()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// リクエストバリデーションチェック
			LoginService::validation_for_grooveonlineregistexecute();
			RegistService::validation_for_grooveonlineregistexecute();

			# リクエストをDTOにセット
			LoginService::set_dto_for_grooveonlineregistexecute();

			# 招待されたユーザの場合のリクエストパラメータをセッションへ格納
			RegistService::set_session_for_grooveonlineregistexecute();

			// --------------------------------------
			// apiサーバにデータを送信し登録します
			// --------------------------------------
			# apiサーバへプロフィール情報を送信(returnでユーザIDを返す必要がある)
			LoginService::send_profile_to_api_undecide();

			// --------------------------------------
			// 画像サーバにデータを送信し登録します
			// --------------------------------------
			# 送信前の画像保存パス
			$tmp_image = \Session::get('tmp_image');
			if ( ! empty($tmp_image))
			{
				$tmp_img_path = \Session::get('tmp_image')->path;
			}
			else
			{
				$tmp_img_path = '';
			}

			# 一時ファイルが存在している
			if (file_exists($tmp_img_path))
			{
				$arr_size = array('34', '64', '126', '252');
				$arr_resize_image_names = array();
				foreach ($arr_size as $size)
				{
					$arr_resize_image_names[$size] = preg_replace('/(\.[a-z]{1,6}$)/i', '_'. $size.'$1', $tmp_img_path);
				}
				$obj_image = new util\Image();
				# 画像サーバへ画像を転送
				$login_dto = LoginDto::get_instance();
				$obj_ftp_movefile_strategy = new util\MoveFileFtpConcreteStrategy(\Config::get('host.img_ftp'), \Config::get('host.img_ftp_user'), \Config::get('host.img_ftp_password'));
				$obj_ftp_movefile_context = new util\MoveFileContext($obj_ftp_movefile_strategy);
				$obj_local_movefile_strategy = new util\MoveFileLocalConcreteStrategy();
				$obj_local_movefile_context = new util\MoveFileContext($obj_local_movefile_strategy);
				unset($size);
				foreach ($arr_resize_image_names as $size => $resize_img_path)
				{
					# tmpディレクトリにアップロードされたファイルをリサイズし再配置
					$obj_image->modify_image($tmp_img_path, $resize_img_path, $size, $size, 'jpg', 0, false);
					// img/profile/user/$user_id/$size/
					$send_image_path = \Config::get('host.img_path'). 'profile/user/'. $login_dto->get_user_id(). '/'.$size.'/'. md5($login_dto->get_user_id()). '.jpg';
					if ( ! $obj_ftp_movefile_context->upload($resize_img_path, $send_image_path))
					{
						$to_path = DOCROOT. 'assets'. DS. 'img'. DS. 'profile'. DS. 'user'. DS. $login_dto->get_user_id(). DS. $size. DS. md5($login_dto->get_user_id()). '.jpg';
						$obj_local_movefile_context->upload($resize_img_path, $to_path);
					}
					unlink($resize_img_path);
				}
				# 画像転送確認後一時ファイルは削除
				unlink($tmp_img_path);

				# 一時画像ファイルのセッションを削除
				\Session::delete('tmp_image');
			}

			// --------------------------------------
			// ユーザ登録確定をAPIへ送信
			// --------------------------------------
			LoginService::send_decide();

			// --------------------------------------
			// ユーザ情報をセッションへ保存します
			// --------------------------------------
			LoginService::set_user_info_to_session_from_dto(true);

			// --------------------------------------
			// リダイレクト
			// --------------------------------------
			# セッションに遷移先urlが存在する場合はリダイレクトさせる
			$redirect = LoginService::get_redirect_url_from_session();
			if (empty($redirect))
			{
				$redirect = \Config::get('host.base_url_http');
			}

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);
			\Response::redirect($redirect);

			return true;
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	/**
	 * パスワード更新のため仮パスワードを発行依頼画面
	 * @throws \Exception
	 */
	public function action_grooveonlinepassreissuerequest()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// -----------------------
			// 不正遷移のチェック
			// -----------------------
			$login_dto = LoginDto::get_instance();
			# ログイン済みのユーザは不正遷移になる
			if ($login_dto->get_user_id())
			{
				# 不正な遷移でのアクセスユー;ザ
				\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
				throw new \Exception('不正な遷移。すでにログインしています');
			}

			# ログイン画面からの遷移ではない場合
			if (isset($_SERVER['HTTP_REFERER']))
			{
				if ( ! preg_match('/\/login[\/index]*$/i', $_SERVER['HTTP_REFERER']))
				{
					\Log::info('ログイン画面経由のアクセスではないのでログイン画面へリダイレクトしました');
					\Log::info($_SERVER['HTTP_REFERER']);
					\Response::redirect('/login');
				}
			}
			else
			{
				\Log::info('HTTP_REFERERの値が存在しないのでログイン画面へリダイレクトしました');
				\Response::redirect('/login');
			}

			$obj_view_model = \ViewModel::forge('login/grooveonlinepassreissuerequest', 'view', null, $this->device. '/login/grooveonlinepassreissuerequest');
			$this->template->title   = "グルーヴオンライン・パスワード再発行";
			$this->template->content = $obj_view_model;
			$this->template->segment = 'login/grooveonlinepassreissuerequest';
			$this->template->hide_to_other_device = true;
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	/**
	 * パスワード再登録メール送信
	 * @throws \Exception
	 */
	public function action_grooveonlinepassreissuesendmail()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// ------------------------------------------------
			// リクエストバリデーション
			// ------------------------------------------------
			LoginService::validation_for_grooveonlinepassreissuesendmail();
			$arr_validate_error = LoginService::get_arr_error();

			// ------------------------------------------------
			// APIへの一時パスワード発行手続きを行う
			// ------------------------------------------------
			if (empty($arr_validate_error))
			{
				// ---------------------------------
				// リクエストをDTOにセット
				// ---------------------------------
				$auth_type = 'grooveonline';
				LoginService::set_dto_for_grooveonlinepassreissuesendmail($auth_type);

				// -------------------------------------
				// APIに仮パスワード発行依頼
				// -------------------------------------
				LoginService::send_password_reissue();

				// -----------------------------------------
				// ユーザに仮パスワードを記載したemailを送信
				// -----------------------------------------
				EmailService::send_reissue_password();

				$obj_view_model = \ViewModel::forge('login/grooveonlinepassreissuesendmail', 'view', null, $this->device. '/login/grooveonlinepassreissuesendmail');
				$obj_view_model->set('arr_error', $arr_validate_error);
				$this->template->title = "グルーヴオンライン・パスワード送信完了";
				$this->template->content = $obj_view_model;
				$this->template->segment = 'login/grooveonlinepassreissuesendmail';
				$this->template->hide_to_other_device = true;
			}
			# エラー存在
			else
			{
				$obj_view_model = \ViewModel::forge('login/grooveonlinepassreissuerequest', 'view', null, $this->device. '/login/grooveonlinepassreissuerequest');
				$obj_view_model->set('arr_error', $arr_validate_error);
				$login_dto = LoginDto::get_instance();
				$obj_view_model->set('hide_send_btn', $login_dto->get_hide_send_btn());
				$this->template->title = "グルーヴオンライン・パスワード再登録";
				$this->template->content = $obj_view_model;
				$this->template->segment = 'login/grooveonlinepassreissuerequest';
				$this->template->hide_to_other_device = true;
			}
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	/**
	 * パスワード再登録画面（メールリンク経由）
	 */
	public function action_grooveonlinepassreissueform($tentative_id)
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// -----------------------
			// 不正遷移のチェック;
			// -----------------------
			$login_dto = LoginDto::get_instance();
			# ログイン済みのユーザは不正遷移になる
			if ($login_dto->get_user_id())
			{
				# 不正な遷移でのアクセスユーザ
				\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
				throw new \Exception('すでにログインしています', 7012); // ログイン済
			}

			// -----------------------
			// Viewにセット
			// -----------------------
			$obj_view_model = \ViewModel::forge('login/grooveonlinepassreissueform', 'view', null, $this->device. '/login/grooveonlinepassreissueform');
			$obj_view_model->set('tentative_id', $tentative_id);
			$this->template->title = "";
			$this->template->content = $obj_view_model;
			$this->template->segment = 'login/grooveonlinepassreissueform';
			$this->template->hide_to_other_device = true;

		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	/**
	 * パスワード更新実行と完了画面
	 */
	public function action_grooveonlinepassreissueupdate()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// バリデーション
			LoginService::validation_for_grooveonlinepassreissueupdate();
			$arr_validate_error = LoginService::get_arr_error();

			if ( ! empty($arr_validate_error))
			{
				$params = http_build_query(\Input::post());
				\Session::set_flash('arr_error', array('password'=> $arr_validate_error['password']));
				\Response::redirect('login/grooveonlinepassreissueform/'. \Input::post('tentative_id'). '/?'. $params);
			}

			// パスワード更新API送信
			LoginService::send_password_update();


			// メール送信 @todo 当面なしでよいかも
			//EmailService::send_reissue_password_done();

			// Viewにセット
			$obj_view_model = \ViewModel::forge('login/index', 'view', null, $this->device. '/login/index');
			$obj_view_model->set('email', \Input::post('email'));
			$obj_view_model->set('from_password_reissue_caption', 'パスワードの変更を受け付けました。'. PHP_EOL. 'あたらしいパスワードを入力してログインしてください。');
			$this->template->title = "";
			$this->template->content = $obj_view_model;
			$this->template->segment = 'login/grooveonlinepassreissueupdate';
			$this->template->hide_to_other_device = true;
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	/**
	 * プロフィール更新画面
	 * 事前条件：ログインしていること
	 */
	public function action_editregistindex()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// バリデーション
			LoginService::validation_for_editregistindex();

			// 初回遷移時のみ
			# 戻るボタンで来た場合ではない
			if ( ! \Input::post('auth_type'))
			{
				// 一時画像を削除
				\Session::delete('tmp_image');

				// ハイレヴェルログインセッションを削除
				\Session::delete('available_login');
			}

			// ----------------------------------
			// ユーザ情報をAPIから取得しDtoに格納
			//-----------------------------------
			# ユーザ情報をapiから取得しdtoに格納
			$login_dto = LoginDto::get_instance();
			$login_dto->set_auth_type(\Input::post('auth_type', 'grooveonline'));
			LoginService::set_user_info_to_dto_from_api();

			// ----------------------------------
			// Viewにセット
			// ----------------------------------
			$obj_view_model = \ViewModel::forge('login/editregistindex', 'view', null, $this->device. '/login/editregistindex');
			$obj_view_model->set('arr_error', array());
			$this->template->title = "グルーヴオンライン・プロフィール編集";
			$this->template->content = $obj_view_model;
			$this->template->segment = 'login/editregistindex';

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

			return \Response::forge($this->template);

		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e, 'login');
		}
	}


	/**
	 *
	 * @throws \Exception
	 * @return boolean
	 */
	public function action_editregistconfirm()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// バリデーション
			LoginService::validation_for_editregistconfirm();
			RegistService::validation_for_editregistconfirm();
			$arr_error = RegistService::get_arr_error();

			if ( ! empty($arr_error))
			{
				\Log::debug('バリデーションエラーが存在します');
				$obj_view_model = \ViewModel::forge('login/editregistindex', 'view', null, $this->device. '/login/editregistindex');
				$obj_view_model->set('arr_error', $arr_error);
				$this->template->title = "グルーヴオンライン・プロフィール更新確認";
				$this->template->content = $obj_view_model;
				$this->template->segment = 'login/editregistindex';

				return \Response::forge($this->template);
			}

			//-----------------------------
			// 画像を処理します
			//-----------------------------
			# アップロードされた画像を取得し/assets/img/tmp/ディレクトリに保存する。その元画像情報をメンバ変数にセット
			if ( ! empty($_FILES['pic']['name']))
			{
				$login_dto = LoginDto::get_instance();
				$prefix_unique_name = md5($login_dto->get_user_id(). 'grooveonline');
				$obj_image = new util\Image();

				if ($obj_image->get_uploaded_tmp_image(null, $prefix_unique_name))
				{
					$arr_uploaded_image_info = $obj_image->get_uploaded_image_info();
					$modify_dir_path = preg_replace('/\/[^\/]+$/', '/', $arr_uploaded_image_info['path']);
					$modify_file_path = $modify_dir_path. \Date::forge()->format('%Y%m%d_'). $prefix_unique_name. '.jpg';

					# tmpディレクトリにアップロードされたファイルをリサイズし再配置
					$obj_image->modify_image($arr_uploaded_image_info['path'], $modify_file_path, 160, 160);
					unlink($arr_uploaded_image_info['path']);

					# 生成された画像ファイル名をセッションに格納
					preg_match('/^.+\/(.+$)/', $modify_file_path, $match);
					$obj_tmp_image = new \stdClass();
					$obj_tmp_image->name = $match[1];
					$obj_tmp_image->path = $modify_file_path;
					$obj_tmp_image->timestamp = \Date::forge()->get_timestamp();
					\Session::set('tmp_image', $obj_tmp_image);
				}
				else
				{
					switch ($obj_image->get_error())
					{
						case \Upload::UPLOAD_ERR_MAX_SIZE:
						case \Upload::UPLOAD_ERR_INI_SIZE:
						case \Upload::UPLOAD_ERR_FORM_SIZE:
							$arr_error['image'] = '画像アップロードに失敗しました。画像サイズが5Mバイトを超えてます';
							break;
						default:
							$arr_error['image'] = '画像アップロードに失敗しました';
					}

					\Log::debug('バリデーションエラーが存在します');
					$obj_view_model = \ViewModel::forge('login/editregistindex', 'view', null, $this->device. '/login/editregistindex');
					$obj_view_model->set('arr_error', $arr_error);
					$this->template->title = "グルーヴオンライン・プロフィール更新確認";
					$this->template->content = $obj_view_model;
					$this->template->segment = 'login/editregistindex';

					return \Response::forge($this->template);

				}
			}

			//-----------------------------
			// view出力します
			//-----------------------------
			# 正常時 (バリデートエラーなし)
			$obj_view_model = \ViewModel::forge('login/editregistconfirm', 'view', null, $this->device. '/login/editregistconfirm');
			$obj_view_model->set('arr_error', $arr_error);
			$this->template->title = "グルーヴオンライン・プロフィール更新確認";
			$this->template->content = $obj_view_model;
			$this->template->segment = 'login/editregistconfirm';

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

			return \Response::forge($this->template);

		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e, 'login');
		}
	}


	public function action_editregistexecute()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			// リクエストバリデーション
			LoginService::validation_for_editregistexecute();
			RegistService::validation_for_editregistexecute();

			$login_dto = LoginDto::get_instance();
			$user_dto  = UserDto::get_instance();

			// --------------------------------------
			// リクエスト値をDTOにセット
			// --------------------------------------
			# POSTリクエストを取得しuser_dtoにセット
			RegistService::set_dto_for_editregistexecute();

			// ---------------------------------------
			// APIサーバにデータを送信し登録
			// ---------------------------------------
			# apiサーバへプロフィール情報を送信(returnでユーザIDを返す必要がある)
			LoginService::send_profile_to_api_undecide();

			// --------------------------------------
			// ユーザ情報をセッションへ保存します
			// --------------------------------------
			LoginService::set_user_info_to_session_from_dto();

			// ハイレヴェルログインセッションを削除
			\Session::delete('available_login');

			// --------------------------------------
			// 画像サーバにデータを送信し登録します
			// --------------------------------------
			# 送信前の画像保存パス
			$tmp_image = \Session::get('tmp_image');
			if (empty($tmp_image))
			{
				\Log::debug('[end]');
				\Log::debug('--------------------------------------'. PHP_EOL);

				# 正常リダイレクト
				\Response::redirect(\Config::get('host.base_url_http'));

			}

			$tmp_img_path = \Session::get('tmp_image')->path;
			if ( ! file_exists($tmp_img_path))
			{
				\Log::debug('[end]');
				\Log::debug('--------------------------------------'. PHP_EOL);

				# 正常リダイレクト
				\Response::redirect(\Config::get('host.base_url_http'));

			}

			$obj_image = new util\Image();
			$prefix_unique_name = md5(__CLASS__. __FUNCTION__.
			\Input::post('email', 'none'). \Input::post('auth_type', 'none'));

			$arr_size = array('34', '64', '126', '252');
			$arr_resize_image_names = array();
			foreach ($arr_size as $size)
			{
				$arr_resize_image_names[$size] = preg_replace('/(\.[a-z]{1,6}$)/i', '_'. $size.'$1', $tmp_img_path);
			}
			$obj_ftp_movefile_strategy   = new util\MoveFileFtpConcreteStrategy(\Config::get('host.img_ftp'), \Config::get('host.img_ftp_user'), \Config::get('host.img_ftp_password'));
			$obj_ftp_movefile_context    = new util\MoveFileContext($obj_ftp_movefile_strategy);
			$obj_local_movefile_strategy = new util\MoveFileLocalConcreteStrategy();
			$obj_local_movefile_context  = new util\MoveFileContext($obj_local_movefile_strategy);
			unset($size);

			# 画像サーバへ画像を転送
			foreach ($arr_resize_image_names as $size => $resize_img_path)
			{
				# tmpディレクトリにアップロードされたファイルをリサイズし再配置
				$obj_image->modify_image($tmp_img_path, $resize_img_path, $size, $size, 'jpg', 0, true);
				// img/profile/user/$user_id/$size/
				$send_image_path = \Config::get('host.img_path'). 'profile/user/'. $login_dto->get_user_id(). '/'.$size.'/'. md5($login_dto->get_user_id()). '.jpg';
				if ( ! $obj_ftp_movefile_context->upload($resize_img_path, $send_image_path))
				{
					$to_path = DOCROOT. 'assets'. DS. 'img'. DS. 'profile'. DS. 'user'. DS. $login_dto->get_user_id(). DS. $size. DS. md5($login_dto->get_user_id()). '.jpg';
					$obj_local_movefile_context->upload($resize_img_path, $to_path);
				}

				unlink($resize_img_path);
			}

			# 画像転送確認後一時ファイルは削除
			unlink($tmp_img_path);

			# 一時画像ファイルのセッションを削除
			\Session::delete('tmp_image');

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

			# 正常リダイレクト
			\Response::redirect(\Config::get('host.base_url_http'));
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e, 'login');
		}
	}


	/**
	 * グルーヴオンラインにログインする
	 * @return boolean
	 */
	public function action_grooveonline()
	{
		try
		{
			\Log::debug('[start]'. __METHOD__);

			LoginService::validation_for_grooveonline();
			$arr_validate_error = LoginService::get_arr_error();

			if ( ! empty($arr_validate_error))
			{
				\Session::set_flash('email', \Input::param('email', null)); // 再入力用メールアドレス
				\Session::set_flash('arr_error', array('email' => 'メールアドレス、パスワードをもう一度ご確認ください。'));
				\Response::redirect('login/index');
			}

			// ログインしていないことが前提なのでユーザセッション情報, DTOを初期化
			LoginService::session_destroy();
			LoginService::clear_dto();

			// リクエスト情報をDTOへ格納
			LoginService::set_dto_for_grooveonline();

			// DTOデータを元にAPI送信（レスポンス情報がDTOへ格納される）
			if (false === LoginService::login_api())
			{
				\Session::set_flash('email', \Input::param('email', null)); // 再入力用メールアドレス
				\Session::set_flash('arr_error', array('email' => 'メールアドレス、パスワードをもう一度ご確認ください。'));
				\Response::redirect('login/index');
			}

			// ログイン情報をセッションへの格納
			LoginService::set_user_info_to_session_from_dto();

			// リダイレクト
			# セッションに遷移先urlが存在する場合はリダイレクトさせる
			$redirect = LoginService::get_redirect_url_from_session();
			if (empty($redirect))
			{
				$redirect = \Config::get('host.base_url_http');
			}

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

			\Response::redirect($redirect);
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	/**
	 * facebookのoauthを利用する
	 *
	 * @throws \Exception
	 * @return boolean
	 */
	public function action_facebook()
	{
		try
		{
			\Log::debug('[start]'. __METHOD__);

			$auth_type = 'facebook';
			$obj_oauth_login = new OauthLoginFactory();
			$obj_oauth_context = $obj_oauth_login->set_oauth_type($auth_type);

			//------------------------
			// 不正遷移アクセスチェック
			//------------------------
			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();
			$user_dto  = UserDto::get_instance();
			# セッションにログイン情報が存在しないこと
			if ($login_dto->get_user_id())
			{
				# 不正な遷移でのアクセスユーザ
				\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
				throw new \Exception('不正な遷移。すでにログイン済み', 9012);
			}

			// -----------------------------------------------
			// グルーヴオンライン・ログイン画面からの遷移
			// Oauth認証URLへのリダイレクト要求
			// -----------------------------------------------
			$code = \Input::param('code');
			if (empty($code)) # facebook_oauth認証からのリダイレクトではない
			{
				# ログインしていないことが前提なので一旦セッション情報を削除
				LoginService::session_destroy();

				// auto_loginチェックボックスの確認
				if (\Input::param('auto_login') == '1')
				{
					# セッションへ自動ログインを設定
					LoginService::set_auto_login_to_session();
				}

				# 招待されたユーザの場合のリクエストパラメータをセッションへ格納
				LoginService::set_session_for_invited();

				\Response::redirect($obj_oauth_context->get_login_url());

				return true;
			}

			// -----------------------------------------------
			// Oauth認証からのリターン遷移
			// グルーヴオンライン・ユーザ登録およびログイン処理
			//------------------------------------------------
			# Oauthユーザ情報がUserDto, LoginDtoへ代入される
			if ( ! $obj_oauth_context->login()) // dtoへのセットだけ
			{
				throw new \Exception('facebook_oauth_response error');
			}

			# auth_type情報をDtoにセット
			LoginService::set_auth_type_to_dto($auth_type);

			// -------------------------
			// ユーザ登録済み
			// -------------------------
			# ユーザ登録済みならログイン処理を実行し、ユーザログイン情報をUserDto, LoginDtoに格納。
			if (LoginService::login_api())
			{
				# ユーザログイン情報をセッションへ格納
				LoginService::set_user_info_to_session_from_dto();
			}
			// -------------------------
			// ユーザ未登録
			// -------------------------
			else
			{
				# APIへユーザ情報を送信し仮登録（登録後にuser_id, login_hashがDTOに格納される）
				LoginService::send_profile_to_api_undecide();

				# APIへユーザ登録確定情報を送信（user_id, login_hashがDTOに格納される）
				LoginService::send_decide();

				# ログイン情報をセッションに保存
				LoginService::set_user_info_to_session_from_dto(true);
			}

			# セッションに遷移先urlが存在する場合はリダイレクトさせる
			$redirect = LoginService::get_redirect_url_from_session();
			if (empty($redirect))
			{
				$redirect = \Config::get('host.base_url_http');
			}

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);
			\Response::redirect($redirect);

			return true;
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	public function action_twitter()
	{
		try
		{
			\Log::debug('[start]'. __METHOD__);

			$auth_type = "twitter";
			$obj_oauth_login = new \login\domain\service\OauthLoginFactory();
			$obj_oauth_context = $obj_oauth_login->set_oauth_type($auth_type);

			//------------------------
			// 不正遷移アクセスチェック
			//------------------------
			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();
			$user_dto  = \user\model\dto\UserDto::get_instance();
			# セッションにログイン情報が存在しないこと
			if ($login_dto->get_user_id())
			{
				# 不正な遷移でのアクセスユーザ
				\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
				throw new \Exception('不正な遷移、すでにログイン済み', 9012);
			}

			// -----------------------------------------------
			// グルーヴオンライン・ログイン画面からの遷移
			// Oauth認証URLへのリダイレクト要求
			// -----------------------------------------------
			$oauth_token = \Input::param('oauth_token');
			if (empty($oauth_token)) # twitter oauth認証からのリダイレクトではない
			{
				# ログインしていないことが前提なので一旦ユーザセッション情報削除
				LoginService::session_destroy();

				// auto_loginチェックボックスの確認
				if (\Input::param('auto_login') == '1')
				{
					# セッションへ自動ログイン設定
					LoginService::set_auto_login_to_session();
				}

				# 招待されたユーザの場合のリクエストパラメータをセッションへ格納
				LoginService::set_session_for_invited();

				\Response::redirect($obj_oauth_context->get_login_url());

				return true;
			}

			// -----------------------------------------------
			// Oauth認証からのリターン遷移
			// グルーヴオンライン登録およびログイン処理
			//------------------------------------------------
			# Oauthユーザ情報がUserDto, LoginDtoへ代入される
			if ( ! $obj_oauth_context->login())
			{
				throw new \Exception('twitter_oauth_response error');
			}

			# LoginDto, UserDtoにauth_typeをセット
			LoginService::set_auth_type_to_dto($auth_type);

			// -------------------------
			// ユーザ登録済み
			// -------------------------
			# ユーザ登録済みならログイン処理を実行し、ユーザログイン情報をUserDto, LoginDtoに格納。
			if (LoginService::login_api())
			{
				# ユーザログイン情報をセッションに格納
				LoginService::set_user_info_to_session_from_dto();
			}
			// -------------------------
			//  ユーザ未登録
			// -------------------------
			else
			{
				# APIへユーザ情報を送信し仮登録（user_id, login_hashがDTOに格納される）
				LoginService::send_profile_to_api_undecide();

				# APIへユーザ登録確定情報を送信（user_id, login_hashがDTOに格納される）
				LoginService::send_decide();

				# ログイン情報をセッションに保存
				LoginService::set_user_info_to_session_from_dto(true);
			}

			# セッションに遷移先urlが存在する場合はリダイレクトさせる
			$redirect = LoginService::get_redirect_url_from_session();
			if (empty($redirect))
			{
				$redirect = \Config::get('host.base_url_http');
			}

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);
			\Response::redirect($redirect);

			return true;
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	public function action_yahoo()
	{
		try
		{
			\Log::debug('[start]'. __METHOD__);

			$auth_type = 'yahoo';
			$obj_oauth_login = new \login\domain\service\OauthLoginFactory();
			$obj_oauth_context = $obj_oauth_login->set_oauth_type($auth_type);

			//------------------------
			// 不正遷移アクセスチェック
			//------------------------
			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();
			$user_dto  = \user\model\dto\UserDto::get_instance();
			# セッションにログイン情報が存在しないこと
			if ($login_dto->get_user_id())
			{
				# 不正な遷移でのアクセスユーザ
				//\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
				throw new \Exception('不正な遷移、すでにログイン済み', 9012);
			}

			// -----------------------------------------------
			// グルーヴオンライン・ログイン画面からの遷移
			// Oauth認証URLへのリダイレクト要求
			// -----------------------------------------------
			$code = \Input::param('code');
			if (empty($code)) # yahoo_oauth認証からのリダレクトではない
			{
				# ログインしていないことが前提なので一旦ユーザセッション情報削除
				LoginService::session_destroy();

				# auto_loginチェックボックスの確認
				if (\Input::param('auto_login') == '1')
				{
					# セッションに自動ログイン設定
					LoginService::set_auto_login_to_session();
				}

				# 招待されたユーザの場合のリクエストパラメータをセッションへ格納
				LoginService::set_session_for_invited();

				\Response::redirect($obj_oauth_context->get_login_url());

				return true;
			}


			// -----------------------------------------------
			// Oauth認証からのリターン遷移
			// グルーヴオンライン登録およびログイン処理
			//------------------------------------------------
			# Oauthユーザ情報がUserDto, LoginDtoへ代入される
			if ( ! $obj_oauth_context->login(\Input::param()))
			{
				throw new \Exception('yahoo_oauth_response error');
			}

			# LoginDto, UserDtoにauth_typeをセット
			LoginService::set_auth_type_to_dto($auth_type);

			// -------------------------
			// ユーザ登録済み
			// -------------------------
			# ユーザ登録済みならログイン処理を実行し、ユーザログイン情報をUserDto, LoginDtoに格納。
			if (LoginService::login_api())
			{
				# ユーザログイン情報をセッションに格納
				LoginService::set_user_info_to_session_from_dto();
			}
			// -------------------------
			//  ユーザ未登録
			// -------------------------
			else
			{
				# APIへユーザ情報を送信し仮登録（user_id, login_hashがDTOに格納される）
				LoginService::send_profile_to_api_undecide();

				# APIへユーザ登録確定情報を送信（user_id, login_hashがDTOに格納される）
				LoginService::send_decide();

				# ログイン情報をセッションに保存
				LoginService::set_user_info_to_session_from_dto(true);
			}

			# セッションに遷移先urlが存在する場合はリダイレクトさせる
			$redirect = LoginService::get_redirect_url_from_session();
			if (empty($redirect))
			{
				$redirect = \Config::get('host.base_url_http');
			}

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);
			\Response::redirect($redirect);

			return true;
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}


	public function action_google()
	{
		try
		{
			\Log::debug('[start]'. __METHOD__);

			$auth_type = 'google';
			$obj_oauth_login = new \login\domain\service\OauthLoginFactory();
			$obj_oauth_context = $obj_oauth_login->set_oauth_type($auth_type);

			//------------------------
			// 不正遷移アクセスチェック
			//------------------------
			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();
			$user_dto  = UserDto::get_instance();

			# セッションにログイン情報が存在しないこと
			if ($login_dto->get_user_id())
			{
				# 不正な遷移でのアクセスユーザ
				\Log::error($login_dto->get_user_name(). '['. $login_dto->get_user_id(). ']');
				throw new \Exception('不正な遷移、すでにログイン済み', 9012);
			}

			// -----------------------------------------------
			// グルーヴオンライン・ログイン画面からの遷移
			// Oauth認証URLへのリダイレクト要求
			// -----------------------------------------------
			$code = \Input::param('code');
			if (empty($code)) # google oauth認証からの遷移ではない
			{
				# ログインしていないことが前提なので一旦ユーザセッション情報削除
				LoginService::session_destroy();

				# auto_loginチェックボックスの確認
				if (\Input::param('auto_login') == '1')
				{
					# セッションに自動ログイン設定
					LoginService::set_auto_login_to_session();
				}

				# 招待されたユーザの場合のリクエストパラメータをセッションへ格納
				LoginService::set_session_for_invited();

				\Response::redirect($obj_oauth_context->get_login_url());

				return true;
			}

			// -----------------------------------------------
			// Oauth認証からのリターン遷移
			// グルーヴオンライン登録およびログイン処理
			//------------------------------------------------
			# Oauthユーザ情報がUserDto, LoginDtoへ代入される
			if ( ! $obj_oauth_context->login(\Input::param()))
			{
				throw new \Exception('google_oauth_response error');
			}

			// LoginDto, UserDtoにauth_typeをセット
			LoginService::set_auth_type_to_dto($auth_type);

			// -------------------------
			// ユーザ登録済み
			// -------------------------
			# ユーザ登録済みならログイン処理を実行し、ユーザログイン情報をUserDto, LoginDtoに格納。
			if (LoginService::login_api())
			{
				# ユーザログイン情報をセッションに格納
				LoginService::set_user_info_to_session_from_dto();
			}
			// -------------------------
			//  ユーザ未登録
			// -------------------------
			else
			{
				# APIへユーザ情報を送信し仮登録（user_id, login_hashがDTOに格納される）
				LoginService::send_profile_to_api_undecide();

				# APIへユーザ登録確定情報を送信（user_id, login_hashがDTOに格納される）
				LoginService::send_decide();

				# ログイン情報をセッションに保存
				LoginService::set_user_info_to_session_from_dto(true);
			}

			# セッションに遷移先urlが存在する場合はリダイレクトさせる
			$redirect = LoginService::get_redirect_url_from_session();
			if (empty($redirect))
			{
				$redirect = \Config::get('host.base_url_http');
			}

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);
			\Response::redirect($redirect);

			return true;
		}
		catch (\Exception $e)
		{
			$this->reflect_exception($e);
		}
	}
}
