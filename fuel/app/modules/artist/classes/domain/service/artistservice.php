<?php
namespace Artist\Domain\Service;

use Artist\Model\Dto\ArtistDto;
use Login\Model\Dto\LoginDto;
use model\dto\CurlDto;
use util\Api;
use Fuel\Core\Validation;
use Review\Model\Dto\ReviewMusicDto;
use login\domain\service\LoginService;
use Tracklist\Model\Dto\TracklistDto;
final class ArtistService
{
	public static function validation_for_detail($id)
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$v = $obj_validation->add('artist_id', 'アーティストID');
		$v->add_rule('required');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', 1);

		$v = $obj_validation->add('tracklist_id', 'トラックリストID');
		$v->add_rule('valid_string', array('numeric'));
		$v->add_rule('numeric_min', 1);

		$arr_params = array(
			'artist_id'    => $id,
			'tracklist_id' => \Input::param('tracklist_id'),
		);

		if ( ! $obj_validation->run($arr_params))
		{
			foreach ($obj_validation->error() as $i => $val)
			{
				throw new \Exception($val->get_message());
			}
		}

		return true;
	}


	public static function validation_for_search($to)
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('AddValidation');

		$v = $obj_validation->add('to', '遷移先アドレス');
		$v->add_rule('check_to_segment_from_artist_search');// 独自ルール

		$v = $obj_validation->add('artist_name', 'アーティスト名');
		$v->add_rule('max_length', '50');

		$arr_params = array(
			'to'          => $to,
			'artist_name' => \Input::param('artist_name'),
		);

		if ( ! $obj_validation->run($arr_params))
		{
			foreach ($obj_validation->error() as $i => $val)
			{
				throw new \Exception($val->get_message());
			}
		}

		return true;
	}


	public static function validation_for_regist($to)
	{
		\Log::debug('[start]'. __METHOD__);

		$obj_validation = Validation::forge();
		$obj_validation->add_callable('AddValidation');

		$v = $obj_validation->add('to', '遷移先アドレス');
		$v->add_rule('check_to_segment_from_artist_search');

		$v = $obj_validation->add('artist_id', 'アーティストID');
		$v->add_rule('required');
		$v->add_rule('max_length', '19');
		$v->add_rule('valid_string', array('numeric'));

		$arr_params = array(
				'to' => $to,
		);

		if ( ! $obj_validation->run($arr_params))
		{
			foreach ($obj_validation->error() as $i => $val)
			{
				throw new \Exception($val->get_message());
			}
		}

		return true;
	}


	public static function set_dto_for_detail($id)
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$review_dto = ReviewMusicDto::get_instance();
		$login_dto  = LoginDto::get_instance();
		$tracklist_dto = TracklistDto::get_instance();

		$artist_dto->set_artist_id(trim($id));
		$review_dto->set_about_id(trim($id));
		$review_dto->set_about('artist_all');
		$review_dto->set_page(1);
		$review_dto->set_limit(5);
		$tracklist_dto->set_tracklist_id(\Input::param('tracklist_id'));
		$tracklist_dto->set_offset(0);
		$tracklist_dto->set_limit(5);

		// ログイン情報をlogin_dtoにセット
		LoginService::set_user_info_to_dto_from_session();

		return true;
	}


	public static function set_dto_for_search($param)
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$artist_dto->set_redirect_segment(htmlentities($param, ENT_QUOTES));
		$artist_dto->set_artist_name(urldecode(\Input::param('artist_name')));

		return true;
	}


	public static function set_dto_for_regist($param)
	{
		\Log::debug('[start]'. __METHOD__);

		$artist_dto = ArtistDto::get_instance();
		$artist_dto->set_artist_id(trim(\Input::post('artist_id')));
		$artist_dto->set_redirect_segment(htmlentities($param, ENT_QUOTES));

		return true;
	}


	public static function get_artist_info()
	{
		\Log::debug('[start]'. __METHOD__);

		$login_dto     = LoginDto::get_instance();
		$artist_dto    = ArtistDto::get_instance();
		$tracklist_dto = TracklistDto::get_instance();

		$arr_send = array(
			'artist_id'        => $artist_dto->get_artist_id(),
			'user_id'          => $login_dto->get_user_id(),
			'login_hash'       => $login_dto->get_login_hash(),
			'tracklist_offset' => $tracklist_dto->get_offset(),
			'tracklist_limit'  => $tracklist_dto->get_limit(),
		);

		if (empty($arr_send['artist_id']))
		{
			return true;
		}

		# CURL送信のための情報をDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url(\Config::get('host.api_url'). 'main/artist/detail.json');
		$curl_dto->set_arr_send($arr_send);

		# CURLにてAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		# CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		if (property_exists($obj_response, 'result') and ( ! empty($obj_response->result)))
		{
			$image_url        = $obj_response->result->image_url;
			$image_small      = $obj_response->result->image_small;
			$image_medium     = $obj_response->result->image_medium;
			$image_large      = $obj_response->result->image_large;
			$image_extralarge = $obj_response->result->image_extralarge;
			$image_url        = empty($image_url)?        \Config::get('image.default.artist.origin'):     $image_url;
			$image_small      = empty($image_small)?      \Config::get('image.default.artist.small'):      $image_small;
			$image_medium     = empty($image_medium)?     \Config::get('image.default.artist.medium'):     $image_medium;
			$image_large      = empty($image_large)?      \Config::get('image.default.artist.large'):      $image_large;
			$image_extralarge = empty($image_extralarge)? \Config::get('image.default.artist.extralarge'): $image_extralarge;

			$artist_dto->set_artist_id($obj_response->result->id);
			$artist_dto->set_artist_name($obj_response->result->name);
			$artist_dto->set_english($obj_response->result->english);
			$artist_dto->set_kana($obj_response->result->kana);
			$artist_dto->set_image_url($image_url);
			$artist_dto->set_image_small($image_small);
			$artist_dto->set_image_medium($image_medium);
			$artist_dto->set_image_large($image_large);
			$artist_dto->set_image_extralarge($image_extralarge);
			$artist_dto->set_url_itunes($obj_response->result->url_itunes);
			$artist_dto->set_url_lastfm($obj_response->result->url_lastfm);
			$artist_dto->set_sort($obj_response->result->sort);
			$artist_dto->set_favorite_status($obj_response->result->favorite_status);
			$artist_dto->set_mbid_itunes($obj_response->result->mbid_itunes);
			$artist_dto->set_mbid_lastfm($obj_response->result->mbid_lastfm);
			$tracklist_dto->set_arr_list($obj_response->result->tracklist);
			$tracklist_dto->set_count($obj_response->result->tracklist_count);
		}

		return true;
	}
}
