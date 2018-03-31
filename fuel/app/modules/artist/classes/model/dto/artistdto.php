<?php
namespace Artist\Model\Dto;

use Model\Dto\BaseDto;
class ArtistDto extends BaseDto
{
	private static $instance = null;

	private $artist_id;
	private $artist_name;
	private $artist_mbid;
	private $kana;
	private $english;
	private $same_names;
	private $mbid_itunes;
	private $mbid_lastfm;
	private $url_itunes;
	private $url_lastfm;
	private $image_url;
	private $image_small;
	private $image_medium;
	private $image_large;
	private $image_extralarge;
	private $favorite_status;
	private $request_code;
	private $redirect_segment;
	private $page;
	private $offset;
	private $limit;
	private $sort;
	private $available_play;
	private $arr_list = array();

	private function __construct()
	{}

	public static function get_instance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function set_artist_id($val)
	{
		$this->artist_id = $val;
	}
	public function get_artist_id()
	{
		return $this->artist_id;
	}

	public function set_artist_name($val)
	{
		$this->artist_name = $val;
	}
	public function get_artist_name()
	{
		return $this->artist_name;
	}

	public function set_artist_mbid($val)
	{
		$this->artist_mbid = $val;
		return true;
	}
	public function get_artist_mbid()
	{
		return $this->artist_mbid;
	}

	public function set_kana($val)
	{
		$this->kana = $val;
	}
	public function get_kana()
	{
		return $this->kana;
	}

	public function set_english($val)
	{
		$this->english = $val;
	}
	public function get_english()
	{
		return $this->english;
	}

	public function set_same_names($val)
	{
		$this->same_names = $val;
	}
	public function get_same_names()
	{
		return $this->same_names;
	}

	public function set_mbid_itunes($val)
	{
		$this->mbid_itunes = $val;
	}
	public function get_mbid_itunes()
	{
		return $this->mbid_itunes;
	}

	public function set_mbid_lastfm($val)
	{
		$this->mbid_lastfm = $val;
	}
	public function get_mbid_lastfm()
	{
		return $this->mbid_lastfm;
	}

	public function set_url_itunes($val)
	{
		$this->url_itunes = $val;
	}
	public function get_url_itunes()
	{
		return $this->url_itunes;
	}

	public function set_url_lastfm($val)
	{
		$this->url_lastfm = $val;
	}
	public function get_url_lastfm()
	{
		return $this->url_lastfm;
	}

	public function set_image_url($val)
	{
		$this->image_url = $val;
	}
	public function get_image_url()
	{
		return $this->image_url;
	}

	public function set_image_small($val)
	{
		$this->image_small = trim($val);
	}
	public function get_image_small()
	{
		return $this->image_small;
	}

	public function set_image_medium($val)
	{
		$this->image_medium = trim($val);
	}
	public function get_image_medium()
	{
		return $this->image_medium;
	}

	public function set_image_large($val)
	{
		$this->image_large = trim($val);
	}
	public function get_image_large()
	{
		return $this->image_large;
	}

	public function set_image_extralarge($val)
	{
		$this->image_extralarge = trim($val);
	}
	public function get_image_extralarge()
	{
		return $this->image_extralarge;
	}

	public function set_sort($val)
	{
		$this->sort = $val;
	}
	public function get_sort()
	{
		return $this->sort;
	}

	public function set_request_code($val)
	{
		$this->request_code = $val;
	}
	public function get_request_code()
	{
		return $this->request_code;
	}

	public function set_redirect_segment($val)
	{
		$this->redirect_segment = $val;
	}
	public function get_redirect_segment()
	{
		return $this->redirect_segment;
	}

	public function set_page($val)
	{
		$this->page = $val;
		return true;
	}
	public function get_page()
	{
		return $this->page;
	}

	public function set_offset($val)
	{
		$this->offset = $val;
	}
	public function get_offset()
	{
		return $this->offset;
	}

	public function set_limit($val)
	{
		$this->limit = $val;
	}
	public function get_limit()
	{
		return $this->limit;
	}

	public function set_favorite_status($val)
	{
		$this->favorite_status = $val;
	}
	public function get_favorite_status()
	{
		return $this->favorite_status;
	}

	public function set_available_play($val)
	{
		$this->available_play = $val;
	}
	public function get_available_play()
	{
		return $this->available_play;
	}

	public function set_arr_list($val)
	{
		$this->arr_list = $val;
	}
	public function get_arr_list()
	{
		return $this->arr_list;
	}




}