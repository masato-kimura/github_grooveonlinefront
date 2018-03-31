<?php
namespace group;

use util;
use group\domain\service\GroupService;
use login\model\dto\LoginDto;
use group\model\dto\GroupDto;
use util\MoveFileFtpConcreteStrategy;
use user\model\dto\UserDto;

final class Controller_Group extends \Controller_Gol_Template
{
	public function before()
	{
		parent::before();
	}

	public function action_index()
	{

	}

	public function action_groupcreate()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();

			//----------------------------------
			// 不正遷移チェック
			//----------------------------------
			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();
			# セッションにログイン情報が存在すること
			$user_id = $login_dto->get_user_id();
			if (empty($user_id))
			{
				throw new \Exception('ログインしてません');
			}

			//----------------------------------
			// カテゴリ取得
			//----------------------------------
			$obj_category = GroupService::get_cateogry_from_api();

			$obj_view_model = \ViewModel::forge('group/groupcreate', 'view', null, $this->device. '/group/groupcreate');
			$obj_view_model->set('arr_error', array());
			$obj_view_model->set('obj_category', $obj_category);
			$this->template->title = "グルーヴオンライン・グループ作成";
			$this->template->content = $obj_view_model;
			$this->template->segment = 'group/groupcreate';
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');
			\Log::debug('[end]'. PHP_EOL);
			\Log::debug('--------------------------------------'. PHP_EOL);
			\Response::redirect('error/general/');
		}
	}

	public function action_groupconfirm()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();

			//----------------------------------
			// 不正遷移チェック
			//----------------------------------
			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();
			# セッションにログイン情報が存在すること
			$user_id = $login_dto->get_user_id();
			if (empty($user_id))
			{
				throw new \Exception('ログインしてません');
			}

			//--------------------------------
			// バリデーション
			//--------------------------------
			$arr_error = GroupService::check_validation();

			//-----------------------------
			// アップロード画像処理
			//-----------------------------
			$obj_image = new util\Image();

			$prefix_unique_name = md5(__CLASS__. __FUNCTION__. $user_id);

			if ( ! empty($_FILES['pic']['name']))
			{
				# アップロードされた画像を取得し/assets/img/tmp/ディレクトリに保存する。その元画像情報をメンバ変数にセット
				if ($obj_image->get_uploaded_tmp_image(null, $prefix_unique_name))
				{
					$arr_uploaded_image_info = $obj_image->get_uploaded_image_info();
					$modify_dir_path = preg_replace('/\/[^\/]+$/', '/', $arr_uploaded_image_info['path']);
					$modify_file_path = $modify_dir_path. \Date::forge()->format('%Y%m%d_'). $prefix_unique_name. '.jpg';

					# tmpディレクトリにアップロードされたファイルをリサイズし再配置
					$obj_image->modify_image($arr_uploaded_image_info['path'], $modify_file_path, 160, 160);

					# 生成された画像ファイル名をセッションに格納
					preg_match('/^.+\/(.+$)/', $modify_file_path, $match);
					\Session::set('tmp_group_image_name', $match[1]);
					\Session::set('tmp_group_image_path',$modify_file_path);
				}
				else
				{
					$arr_error['image'] = '画像アップロードに失敗しました';
				}
			}

			//-----------------------------
			// 3. view出力します
			//-----------------------------
			# 正常時 (バリデートエラーなし)
			if (empty($arr_error))
			{
				# 確認画面を表示
				$obj_category = GroupService::get_cateogry_from_api();
				$obj_view_model = \ViewModel::forge('group/groupconfirm', 'view', null, $this->device. '/group/groupconfirm');
				$obj_view_model->set('obj_category', $obj_category);
				$this->template->title = "グルーヴオンライン・グループ作成";
				$this->template->content = $obj_view_model;
				$this->template->segment = 'group/groupconfirm';
			}
			# バリデーションエラー有り時
			else
			{
			# 入力画面へ戻る
				$obj_category = GroupService::get_cateogry_from_api();
				$obj_view_model = \ViewModel::forge('group/groupcreate', 'view', null, $this->device. '/group/groupcreate');
				$obj_view_model->set('arr_error', $arr_error);
				$obj_view_model->set('obj_category', $obj_category);
				$this->template->title = "グルーヴオンライン・グループ作成";
				$this->template->content = $obj_view_model;
			}

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);

		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');
			\Log::debug('[end]'. PHP_EOL);
			\Log::debug('--------------------------------------'. PHP_EOL);
			\Response::redirect('error/general/');
		}
	}

	public function action_groupdone()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

			//----------------------------------
			// 不正遷移チェック
			//----------------------------------
			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();
			# セッションにログイン情報が存在すること
			$user_id = $login_dto->get_user_id();
			if (empty($user_id))
			{
				throw new \Exception('ログインしてません');
			}

			//--------------------------------
			// バリデーション
			//--------------------------------
			$arr_error = GroupService::check_validation();
			if ($arr_error)
			{
				throw new \Exception('登録に失敗しました');
			}

			# dtoにセット
			GroupService::set_group_to_dto_from_postrequest();
			$group_dto = GroupDto::get_instance();

			# apiへ送信
			GroupService::send_group_to_api();

			# 画像サーバに送信
			$tmp_img_path = \Session::get('tmp_group_image_path');
			if (file_exists($tmp_img_path))
			{
				// img/profile/group/$group_id/
				$send_image_path = \Config::get('host.img_path'). 'profile'. DS. 'group'. DS. $group_dto->get_group_id(). DS. md5($group_dto->get_group_id()). '.jpg';

				# 画像サーバへ画像を転送
				$obj_ftp_movefile_strategy = new util\MoveFileFtpConcreteStrategy(\Config::get('host.img_ftp'), \Config::get('host.img_ftp_user'), \Config::get('host.img_ftp_password'));
				$obj_ftp_movefile_context = new util\MoveFileContext($obj_ftp_movefile_strategy);
				if ( ! $obj_ftp_movefile_context->upload($tmp_img_path, $send_image_path))
				{
					$obj_local_movefile_strategy = new util\MoveFileLocalConcreteStrategy();
					$obj_local_movefile_context = new util\MoveFileContext($obj_local_movefile_strategy);
					$to_path = DOCROOT. 'assets'. DS. 'img'. DS. 'profile'. DS. 'group'. DS. $group_dto->get_group_id(). DS. md5($group_dto->get_group_id()). '.jpg';
					$obj_local_movefile_context->upload($tmp_img_path, $to_path);
				}

				# 一時画像ファイルのセッションを削除
				\Session::delete('tmp_group_image_name');
				\Session::delete('tmp_group_image_file');

				# 画像転送確認後一時ファイルは削除
				unlink($tmp_img_path);
			}

			# カテゴリマスタ取得
			$obj_view_model = \ViewModel::forge('group/groupdone', 'view', null, $this->device. '/group/groupdone');
			$this->template->title = "グルーヴオンライン・グループ完了";
			$this->template->content = $obj_view_model;
			$this->template->segment = 'group/groupdone';

		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');
			\Log::debug('[end]'. PHP_EOL);
			\Log::debug('--------------------------------------'. PHP_EOL);
			\Response::redirect('error/general/');
		}
	}

	public function action_groupedit($group_id)
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

			//----------------------------------
			// 不正遷移チェック
			//----------------------------------
			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();
			# セッションにログイン情報が存在すること
			$user_id = $login_dto->get_user_id();
			if (empty($user_id))
			{
				throw new \Exception('ログインしてません');
			}

			//--------------------------------
			// バリデーション
			//--------------------------------
			if (empty($user_id))
			{
				throw new \Exception('user_id is empty');
			}
			if (empty($group_id))
			{
				throw new \Exception('group_id is empty');
			}

			//----------------------------------
			// カテゴリ取得
			//----------------------------------
			$obj_category = GroupService::get_cateogry_from_api();

			# GroupDtoにセット
			$group_dto = GroupDto::get_instance();
			$group_dto->set_group_id($group_id);

			GroupService::get_group_info();

			$obj_view_model = \ViewModel::forge('group/groupedit', 'view', null, $this->device. '/group/groupedit');
			$this->template->title = "グルーヴオンライン・グループ完了";
			$obj_view_model->set('obj_category', $obj_category);
			$this->template->content = $obj_view_model;
			$this->template->segment = 'group/groupedit';

		}
		catch (\Exception $e)
		{
			var_dump($e->getMessage());
		}
	}

	public function action_groupeditconfirm()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();

			//----------------------------------
			// 不正遷移チェック
			//----------------------------------
			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();
			# セッションにログイン情報が存在すること
			$user_id = $login_dto->get_user_id();
			if (empty($user_id))
			{
				throw new \Exception('ログインしてません');
			}

			//--------------------------------
			// バリデーション
			//--------------------------------
			$arr_error = GroupService::check_validation(true);

			//-----------------------------
			// アップロード画像処理
			//-----------------------------
			$obj_image = new util\Image();

			$prefix_unique_name = md5(__CLASS__. __FUNCTION__. $user_id);

			if ( ! empty($_FILES['pic']['name']))
			{
				# アップロードされた画像を取得し/assets/img/tmp/ディレクトリに保存する。その元画像情報をメンバ変数にセット
				if ($obj_image->get_uploaded_tmp_image(null, $prefix_unique_name))
				{
					$arr_uploaded_image_info = $obj_image->get_uploaded_image_info();
					$modify_dir_path = preg_replace('/\/[^\/]+$/', '/', $arr_uploaded_image_info['path']);
					$modify_file_path = $modify_dir_path. \Date::forge()->format('%Y%m%d_'). $prefix_unique_name. '.jpg';

					# tmpディレクトリにアップロードされたファイルをリサイズし再配置
					$obj_image->modify_image($arr_uploaded_image_info['path'], $modify_file_path, 160, 160);

					# 生成された画像ファイル名をセッションに格納
					preg_match('/^.+\/(.+$)/', $modify_file_path, $match);
					\Session::set('tmp_group_image_name', $match[1]);
					\Session::set('tmp_group_image_path',$modify_file_path);
				}
				else
				{
					$arr_error['image'] = '画像アップロードに失敗しました';
				}
			}

			//-----------------------------
			// 3. view出力します
			//-----------------------------
			# 正常時 (バリデートエラーなし)
			if (empty($arr_error))
			{
				# 確認画面を表示
				$obj_category = GroupService::get_cateogry_from_api();
				$obj_view_model = \ViewModel::forge('group/groupeditconfirm', 'view', null, $this->device. '/group/groupeditconfirm');
				$obj_view_model->set('obj_category', $obj_category);
				$this->template->title = "グルーヴオンライン・グループ作成";
				$this->template->content = $obj_view_model;
				$this->template->segment = 'group/groupeditconfirm';
			}
			# バリデーションエラー有り時
			else
			{
				# 入力画面へ戻る
				# GroupDtoにセット
				$group_dto = GroupDto::get_instance();
				$group_dto->set_group_id(\Input::post('group_id'));

				GroupService::get_group_info();

				$obj_category = GroupService::get_cateogry_from_api();
				$obj_view_model = \ViewModel::forge('group/groupedit', 'view', null, $this->device. '/group/groupedit');
				$obj_view_model->set('arr_error', $arr_error);
				$obj_view_model->set('obj_category', $obj_category);
				$this->template->title = "グルーヴオンライン・グループ作成";
				$this->template->content = $obj_view_model;
			}

			\Log::debug('[end]');
			\Log::debug('--------------------------------------'. PHP_EOL);
		}
		catch (\Exception $e)
		{
			var_dump($e->getMessage());
		}

	}

	public function action_groupeditdone()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

			//----------------------------------
			// 不正遷移チェック
			//----------------------------------
			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();
			# セッションにログイン情報が存在すること
			$user_id = $login_dto->get_user_id();
			if (empty($user_id))
			{
				throw new \Exception('ログインしてません');
			}

			//--------------------------------
			// バリデーション
			//--------------------------------
			$arr_error = GroupService::check_validation(true);
			if ($arr_error)
			{
				throw new \Exception('登録に失敗しました');
			}

			# dtoにセット
			GroupService::set_group_to_dto_from_postrequest();
			$group_dto = GroupDto::get_instance();

			# apiへ送信
			GroupService::send_edit_group_to_api();

			# 画像サーバに送信
			$tmp_img_path = \Session::get('tmp_group_image_path');
			if (file_exists($tmp_img_path))
			{
				// img/profile/group/$group_id/
				$send_image_path = \Config::get('host.img_path'). 'profile'. DS. 'group'. DS. $group_dto->get_group_id(). DS. md5($group_dto->get_group_id()). '.jpg';

				# 画像サーバへ画像を転送
				$obj_ftp_movefile_strategy = new util\MoveFileFtpConcreteStrategy(\Config::get('host.img_ftp'), \Config::get('host.img_ftp_user'), \Config::get('host.img_ftp_password'));
				$obj_ftp_movefile_context = new util\MoveFileContext($obj_ftp_movefile_strategy);
				if ( ! $obj_ftp_movefile_context->upload($tmp_img_path, $send_image_path))
				{
					$obj_local_movefile_strategy = new util\MoveFileLocalConcreteStrategy();
					$obj_local_movefile_context = new util\MoveFileContext($obj_local_movefile_strategy);
					$to_path = DOCROOT. 'assets'. DS. 'img'. DS. 'profile'. DS. 'group'. DS. $group_dto->get_group_id(). DS. md5($group_dto->get_group_id()). '.jpg';
					$obj_local_movefile_context->upload($tmp_img_path, $to_path);
				}

				# 一時画像ファイルのセッションを削除
				\Session::delete('tmp_group_image_name');
				\Session::delete('tmp_group_image_file');

				# 画像転送確認後一時ファイルは削除
				unlink($tmp_img_path);
			}

			$obj_view_model = \ViewModel::forge('group/groupeditdone', 'view', null, $this->device. '/group/groupeditdone');
			$this->template->title = "グルーヴオンライン・グループ完了";
			$this->template->content = $obj_view_model;
			$this->template->segment = 'group/groupeditdone';

		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');
			\Log::debug('[end]'. PHP_EOL);
			\Log::debug('--------------------------------------'. PHP_EOL);
			\Response::redirect('error/general/');
		}
	}

	public function action_groupdelete()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		}
		catch (\Exception $e)
		{
			var_dump($e->getMessage());
		}
	}

	public function action_memberadd()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

			//----------------------------------
			// 不正遷移チェック
			//----------------------------------
			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();

			# セッションにログイン情報が存在すること
			$user_id = $login_dto->get_user_id();
			if (empty($user_id))
			{
				throw new \Exception('ログインしてません');
			}

			$group_id = \Input::post('group_id');
			$member_name = \Input::post('name');

			//----------------------------------
			// バリデーション
			//----------------------------------
			if (empty($group_id))
			{
				throw new \Exception('group_id is empty');
			}
			if (empty($member_name))
			{
				throw new \Exception('member_name is empty');
			}

			//---------------------------------
			// APIへ送信
			//---------------------------------
			# Dtoにセット
			$group_dto = GroupDto::get_instance();
			$group_dto->set_group_id($group_id);
			$group_dto->set_members(array(
				0 =>
					array(
						'name' => $member_name,
					),
				)
			);

			GroupService::send_member_add();

			\Response::redirect('/group/groupedit/'. $group_id. '/');

		}
		catch (\Exception $e)
		{
			var_dump($e->getMessage());
		}
	}

	public function action_memberdel()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

			//----------------------------------
			// 不正遷移チェック
			//----------------------------------
			# before()でセッション情報が代入済み@Controller_Gol_Template
			$login_dto = LoginDto::get_instance();
			# セッションにログイン情報が存在すること
			$user_id = $login_dto->get_user_id();
			if (empty($user_id))
			{
				throw new \Exception('ログインしてません');
			}

			$group_id = \Input::post('group_id');
			$member_id = \Input::post('member_id'); // user_id

			//--------------------------------
			// バリデーション
			//--------------------------------
			if (empty($user_id))
			{
				throw new \Exception('user_id is empty');
			}
			if (empty($group_id))
			{
				throw new \Exception('group_id is empty');
			}

			if (empty($member_id))
			{
				throw new \Exception('group_id is empty');
			}

			//---------------------------------
			// APIへ送信
			//---------------------------------
			# GroupDtoにセット
			$group_dto = GroupDto::get_instance();
			$group_dto->set_group_id($group_id);

			$arr_member_id = array($member_id);
			GroupService::send_member_delete($arr_member_id);

			\Response::redirect('/group/groupedit/'. $group_id. '/');
		}
		catch (\Exception $e)
		{
			var_dump($e->getMessage());
			//var_dump($e->getFile(). $e->getLine());
			return false;
		}
	}

}