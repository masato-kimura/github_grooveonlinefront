<?php
namespace Api;

use Api\Domain\Service\ReviewMusicService;
use Login\Model\Dto\LoginDto;
use Review\Model\Dto\ReviewMusicDto;
use Review\Model\Dto\CoolDto;
use Review\Model\Dto\UserCommentDto;
use Review\Model\Dto\CommentDto;

final class Controller_Review extends \Controller_Rest
{
	/**
	 * レビューを投稿する
	 * @throws \Exception
	 * @return boolean
	 */
	public function post_set()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			ReviewMusicService::get_json_request();

			# バリデーションチェック
			ReviewMusicService::validation_for_set();

			# DTOにリクエストをセット
			ReviewMusicService::set_dto_for_set();

			# 未ログイン時
			$login_dto = LoginDto::get_instance();
			$user_id = $login_dto->get_user_id();
			if (empty($user_id))
			{
				# ログイン前でユーザ情報が取得できない場合はセッションに保存
				ReviewMusicService::set_session_tmp_review();

				# APIレスポンス
				$arr_response = array(
					'success'  => false,
					'code'     => 7010,
					'response' => 'ログインしてません。セッションに保存しました',
					'result'   => null,
				);
			}
			# ログイン時
			else
			{
				# APIへレビューを送信
				ReviewMusicService::send_write_to_api();

				$review_music_dto = ReviewMusicDto::get_instance();

				# APIレスポンス
				$arr_response = array(
					'success'  => true,
					'code'     => '1001',
					'response' => 'send_review_done',
					'result'   => array(
						'album_id'   => $review_music_dto->get_album_id(),
						'review_id'  => $review_music_dto->get_review_id(),
						'updated_at' => $review_music_dto->get_updated_at(),
					),
				);

				# レビューセッションを削除
				\Session::delete('tmp_review');
			}

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
		}
		catch (\Exception $e)
		{
			# セッション情報を削除
			\Session::delete('tmp_review');

			$arr_response = array('result' => null, 'success' => false, 'code' => $e->getCode(), 'response' => $e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');
			\Log::error($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}


	public function post_one()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			ReviewMusicService::get_json_request();

			# バリデーションチェック
			ReviewMusicService::validation_for_one();

			# DTOにリクエストをセット
			ReviewMusicService::set_dto_for_one();

			# APIへレビューを送信
			ReviewMusicService::get_review_one();

			$review_music_dto = ReviewMusicDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'get_review',
				'result'   => $review_music_dto->get_arr_list(),
			);

			\Log::debug('[end]'. PHP_EOL. PHP_EOL);

			$this->response($arr_response);
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			$arr_response = array(
				'success' => false,
				'code' => $e->getCode(),
				'response' => $e->getMessage(),
				'result' => null,
			);

			$this->response($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}


	/**
	 * いいぜ情報を送る
	 */
	public function post_sendcool()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			ReviewMusicService::get_json_request();

			# バリデーションチェック
			ReviewMusicService::validation_for_sendcool();

			# DTOにリクエストをセット
			ReviewMusicService::set_dto_for_sendcool();

			# APIへレビューを送信
			ReviewMusicService::send_cool_to_api();

			# クッキーへ保存
			ReviewMusicService::set_cool_to_session();

			$cool_dto = CoolDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success' => true,
				'code'=> '1001',
				'response' => 'send_cool_done',
				'result' => array(
					'reflection'     => $cool_dto->get_reflection(),
					'cool_count'     => $cool_dto->get_cool_count(),
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			# APIレスポンス
			$arr_response = array(
				'success' => false,
				'code'=> '',
				'response' => $e->getMessage(),
				'result' => null,
			);

			$this->response($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}


	/**
	 * いいぜ情報を取得
	 */
	public function post_getcool()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			ReviewMusicService::get_json_request();

			# バリデーションチェック
			ReviewMusicService::validation_for_getcool();

			# DTOにリクエストをセット
			ReviewMusicService::set_dto_for_getcool();

			# APIから取得
			ReviewMusicService::get_cool_from_api();

			# 画像情報をセット
			ReviewMusicService::set_image_url_to_cool_users();

			$cool_dto = CoolDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success' => true,
				'code'=> '1001',
				'response' => 'get_cool_done',
				'result' => array(
					'all_count'  => $cool_dto->get_all_count(),
					'arr_list'   => $cool_dto->get_arr_list(),
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			# APIレスポンス
			$arr_response = array(
			'success' => false,
			'code'=> '',
			'response' => $e->getMessage(),
			'result' => null,
			);

			$this->response($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}


	/**
	 * レビューコメント情報を送る
	 */
	public function post_setusercomment()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			ReviewMusicService::get_json_request();

			# バリデーションチェック
			ReviewMusicService::validation_for_setusercomment();

			# DTOにリクエストをセット
			ReviewMusicService::set_dto_for_setusercomment();

			# APIへレビューを送信
			ReviewMusicService::setusercomment_to_api();

			$user_comment_dto = UserCommentDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success' => true,
				'code'=> '1001',
				'response' => 'set usercomment_done',
				'result' => array(
					'is_done'     => true,
					'id'          => $user_comment_dto->get_user_comment_id(),
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			# APIレスポンス
			$arr_response = array(
			'success' => false,
			'code'=> '',
			'response' => $e->getMessage(),
			'result' => null,
			);

			$this->response($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}


	/**
	 * レビューコメント削除情報を送る
	 */
	public function post_removeusercomment()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			ReviewMusicService::get_json_request();

			# バリデーションチェック
			ReviewMusicService::validation_for_removeusercomment();

			# DTOにリクエストをセット
			ReviewMusicService::set_dto_for_removeusercomment();

			# APIへレビューを送信
			ReviewMusicService::removeusercomment_to_api();

			$user_comment_dto = UserCommentDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'remove usercomment_done',
				'result'   => array(
					'is_done' => true,
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			# リクエストのオブジェクト化
			ReviewMusicService::get_json_request();

			# APIレスポンス
			$arr_response = array(
			'success' => false,
			'code'=> '',
			'response' => $e->getMessage(),
			'result' => null,
			);

			$this->response($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}


	public function post_sendcomment()
	{
		\Log::debug('--------------------------------------');
		\Log::debug('[start]'. __METHOD__);

		try
		{
			# リクエストのオブジェクト化
			ReviewMusicService::get_json_request();

			# バリデーションチェック
			ReviewMusicService::validation_for_sendcomment();

			# DTOにリクエストをセット
			ReviewMusicService::set_dto_for_sendcomment();

			# APIへレビューを送信
			ReviewMusicService::sendcomment_to_api();

			# APIレスポンス
			$comment_dto = CommentDto::get_instance();
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'remove usercomment_done',
				'result'   => array(
					'reflection' => true,
					'comment_id' => $comment_dto->get_comment_id(),
					'count'      => $comment_dto->get_count(),
					'arr_list'   => $comment_dto->get_arr_list(),
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			# APIレスポンス
			$arr_response = array(
				'success'  => false,
				'code'     => '',
				'response' => $e->getMessage(),
				'result'   => null,
			);

			$this->response($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}


	public function post_deletecomment()
	{
		\Log::debug('--------------------------------------');
		\Log::debug('[start]'. __METHOD__);

		try
		{
			# リクエストのオブジェクト化
			ReviewMusicService::get_json_request();

			# バリデーションチェック
			ReviewMusicService::validation_for_deletecomment();

			# DTOにリクエストをセット
			ReviewMusicService::set_dto_for_deletecomment();

			# APIへレビューを送信
			ReviewMusicService::deletecomment_to_api();

			# APIレスポンス
			$comment_dto = CommentDto::get_instance();
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'delete usercomment_done',
				'result'   => array(
					'reflection' => true,
					'count'      => $comment_dto->get_count(),
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			# APIレスポンス
			$arr_response = array(
				'success'  => false,
				'code'     => '',
				'response' => $e->getMessage(),
				'result'   => null,
			);

			$this->response($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}


	public function post_getcomment()
	{
		\Log::debug('--------------------------------------');
		\Log::debug('[start]'. __METHOD__);

		try
		{
			# リクエストのオブジェクト化
			ReviewMusicService::get_json_request();

			# バリデーションチェック
			ReviewMusicService::validation_for_getcomment();

			# DTOにリクエストをセット
			ReviewMusicService::set_dto_for_getcomment();

			# APIへレビューを送信
			ReviewMusicService::getcomment_to_api();

			# フォーマット
			ReviewMusicservice::getcomment_format();

			# APIレスポンス
			$comment_dto = CommentDto::get_instance();
			$arr_response = array(
					'success'  => true,
					'code'     => '1001',
					'response' => 'get comment_done',
					'result'   => array(
							'review_id' => $comment_dto->get_review_id(),
							'count'     => $comment_dto->get_count(),
							'arr_list'  => $comment_dto->get_arr_list(),
					),
			);
			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error($e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');

			# APIレスポンス
			$arr_response = array(
				'success'  => false,
				'code'     => '',
				'response' => $e->getMessage(),
				'result'   => null,
			);

			$this->response($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}

	/**
	 * リスト取得
	 * @throws \Exception
	 * @return boolean
	 */
	public function post_getlist()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			# リクエストのオブジェクト化
			ReviewMusicService::get_json_request();

			# バリデーションチェック
			ReviewMusicService::validation_for_getlist();

			# DTOにリクエストをセット
			ReviewMusicService::set_dto_for_getlist();

			# APIから取得
			ReviewMusicService::get_list_from_api();

			$review_dto = ReviewMusicDto::get_instance();

			# APIレスポンス
			$arr_response = array(
				'success'  => true,
				'code'     => '1001',
				'response' => 'get tracklist done',
				'result'   => array(
					'arr_list' => $review_dto->get_arr_list(),
					'count'    => $review_dto->get_review_count(),
				),
			);

			$this->response($arr_response);
			\Log::debug('[end]'. PHP_EOL. PHP_EOL);
		}
		catch (\Exception $e)
		{
			$arr_response = array('result' => null, 'success' => false, 'code' => $e->getCode(), 'response' => $e->getMessage());
			\Log::error($e->getFile(). '['. $e->getLine(). ']');
			\Log::error($arr_response);
			\Log::error('[end]'. PHP_EOL. PHP_EOL);

			return false;
		}
	}


}
