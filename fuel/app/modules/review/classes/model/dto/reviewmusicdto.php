<?php
namespace Review\Model\Dto;

class ReviewMusicDto
{
	private static $instance = null;

	private $review_user_id;
	private $review_id;
	private $artist_id;
	private $artist_name;
	private $artist_kana;
	private $artist_mbid_itunes;
	private $artist_mbid_lastfm;
	private $artist_url;
	private $artist_review;
	private $artist_star;
	private $artist_updated_at;
	private $album_id;
	private $album_name;
	private $album_name_hidden;
	private $album_mbid_itunes;
	private $album_mbid_lastfm;
	private $album_image;
	private $album_url;
	private $album_review;
	private $album_star;
	private $album_updated_at;
	private $track_id;
	private $track_name;
	private $track_name_hidden;
	private $track_mbid_itunes;
	private $track_mbid_lastfm;
	private $track_url;
	private $track_review;
	private $track_star;
	private $track_updated_at;
	private $tracks;
	private $content;
	private $link;
	private $about;
	private $top_about;
	private $about_id;
	private $search_word;
	private $review;
	private $star;
	private $updated_at;

	private $page;
	private $offset = 0;
	private $limit;
	private $top_limit;
	private $sort;
	private $arr_list = array();
	private $arr_list_sub = array();
	private $arr_top_list = array();
	private $is_delete;
	private $review_count;
	private $cool_count;
	private $comment_count;
	private $user_image;
/*
	private $track_album_name;
	private $track_album_mbid_itunes;
	private $track_album_url;
	private $track_album_artist;
*/
	private function __construct() {}

	public static function get_instance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function set_review_user_id($val)
	{
		$this->review_user_id = $val;
	}
	public function get_review_user_id()
	{
		return $this->review_user_id;
	}

	public function set_review_id($val)
	{
		$this->review_id = $val;
	}
	public function get_review_id()
	{
		return $this->review_id;
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

	public function set_artist_image($val)
	{
		$this->artist_image = $val;
	}
	public function get_artist_image()
	{
		return $this->artist_image;
	}

	public function set_artist_kana($val)
	{
		$this->artist_kana = $val;
	}
	public function get_artist_kana()
	{
		return $this->artist_kana;
	}

	public function set_artist_mbid_itunes($val)
	{
		$this->artist_mbid_itunes = $val;
	}
	public function get_artist_mbid_itunes()
	{
		return $this->artist_mbid_itunes;
	}

	public function set_artist_mbid_lastfm($val)
	{
		$this->artist_mbid_lastfm = $val;
	}
	public function get_artist_mbid_lastfm()
	{
		return $this->artist_mbid_lastfm;
	}

	public function set_artist_url($val)
	{
		$this->artist_url = $val;
	}
	public function get_artist_url()
	{
		return $this->artist_url;
	}

	public function set_artist_review($val)
	{
		$this->artist_review = $val;
	}
	public function get_artist_review()
	{
		return $this->artist_review;
	}

	public function set_artist_star($val)
	{
		$this->artist_star = $val;
	}
	public function get_artist_star()
	{
		return $this->artist_star;
	}
	public function set_artist_updated_at($val)
	{
		$this->artist_updated_at = $val;
	}
	public function get_artist_updated_at()
	{
		return $this->artist_updated_at;
	}

	public function set_album_id($val)
	{
		$this->album_id = $val;
	}
	public function get_album_id()
	{
		return $this->album_id;
	}

	public function set_album_name($val)
	{
		$this->album_name = $val;
	}
	public function get_album_name()
	{
		return $this->album_name;
	}

	public function set_album_name_hidden($val)
	{
		$this->album_name_hidden = $val;
	}
	public function get_album_name_hidden()
	{
		return $this->album_name_hidden;
	}

	public function set_album_mbid_itunes($val)
	{
		$this->album_mbid_itunes = $val;
	}
	public function get_album_mbid_itunes()
	{
		return $this->album_mbid_itunes;
	}

	public function set_album_mbid_lastfm($val)
	{
		$this->album_mbid_lastfm = $val;
	}
	public function get_album_mbid_lastfm()
	{
		return $this->album_mbid_lastfm;
	}

	public function set_album_image($val)
	{
		$this->album_image = $val;
	}
	public function get_album_image()
	{
		return $this->album_image;
	}

	public function set_album_url($val)
	{
		$this->album_url = $val;
	}
	public function get_album_url()
	{
		return $this->album_url;
	}

	public function set_album_review($val)
	{
		$this->album_review = $val;
	}
	public function get_album_review()
	{
		return $this->album_review;
	}

	public function set_album_star($val)
	{
		$this->album_star = $val;
	}
	public function get_album_star()
	{
		return $this->album_star;
	}

	public function set_album_updated_at($val)
	{
		$this->album_updated_at = $val;
	}
	public function get_album_updated_at()
	{
		return $this->album_updated_at;
	}

	public function set_track_id($val)
	{
		$this->track_id = $val;
	}
	public function get_track_id()
	{
		return $this->track_id;
	}

	public function set_track_name($val)
	{
		$this->track_name = $val;
	}
	public function get_track_name()
	{
		return $this->track_name;
	}

	public function set_track_name_hidden($val)
	{
		$this->track_name_hidden = $val;
	}
	public function get_track_name_hidden()
	{
		return $this->track_name_hidden;
	}

	public function set_track_mbid_itunes($val)
	{
		$this->track_mbid_itunes = $val;
	}
	public function get_track_mbid_itunes()
	{
		return $this->track_mbid_itunes;
	}

	public function set_track_mbid_lastfm($val)
	{
		$this->track_mbid_lastfm = $val;
	}
	public function get_track_mbid_lastfm()
	{
		return $this->track_mbid_lastfm;
	}

	public function set_track_url($val)
	{
		$this->track_url = $val;
	}
	public function get_track_url()
	{
		return $this->track_url;
	}

	public function set_tracks($val)
	{
		$this->tracks = $val;
	}
	public function get_tracks()
	{
		return $this->tracks;
	}

	public function set_track_review($val)
	{
		$this->track_review = $val;
	}
	public function get_track_review()
	{
		return $this->track_review;
	}

	public function set_track_star($val)
	{
		$this->track_star = $val;
	}
	public function get_track_star()
	{
		return $this->track_star;
	}

	public function set_track_updated_at($val)
	{
		$this->track_updated_at = $val;
	}
	public function get_track_updated_at()
	{
		return $this->track_updated_at;
	}

	public function set_content($val)
	{
		$this->content = $val;
	}
	public function get_content()
	{
		return $this->content;
	}

	public function set_link($val)
	{
		$this->link = $val;
	}
	public function get_link()
	{
		return $this->link;
	}

	public function set_about($val)
	{
		$this->about = $val;
	}
	public function get_about()
	{
		return $this->about;
	}

	public function set_top_about($val)
	{
		$this->top_about = $val;
	}
	public function get_top_about()
	{
		return $this->top_about;
	}

	public function set_about_id($val)
	{
		$this->about_id = $val;
	}
	public function get_about_id()
	{
		return $this->about_id;
	}

	public function set_search_word($val)
	{
		$this->search_word = $val;
	}
	public function get_search_word()
	{
		return $this->search_word;
	}

	public function set_star($val)
	{
		$this->star = $val;
	}
	public function get_star()
	{
		return $this->star;
	}

	public function set_updated_at($val)
	{
		$this->updated_at = $val;
	}
	public function get_updated_at()
	{
		return $this->updated_at;
	}

	public function set_review($val)
	{
		$this->review = $val;
	}
	public function get_review()
	{
		return $this->review;
	}

	public function set_arr_list($val)
	{
		$this->arr_list = $val;
	}
	public function get_arr_list()
	{
		return $this->arr_list;
	}

	public function set_arr_list_sub($val)
	{
		$this->arr_list_sub = $val;
	}
	public function get_arr_list_sub()
	{
		return $this->arr_list_sub;
	}

	public function set_arr_top_list($val)
	{
		$this->arr_top_list = $val;
	}
	public function get_arr_top_list()
	{
		return $this->arr_top_list;
	}

	public function set_is_delete($val)
	{
		$this->is_delete = $val;
	}
	public function get_is_delete()
	{
		return $this->is_delete;
	}

	public function set_sort($val)
	{
		$this->sort = $val;
	}
	public function get_sort()
	{
		return $this->sort;
	}

	public function set_page($val)
	{
		$this->page = $val;
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

	public function set_top_limit($val)
	{
		$this->top_limit = $val;
	}
	public function get_top_limit()
	{
		return $this->top_limit;
	}

	public function set_review_count($val)
	{
		$this->review_count = $val;
	}
	public function get_review_count()
	{
		return $this->review_count;
	}

	public function set_cool_count($val)
	{
		$this->cool_count = $val;
	}
	public function get_cool_count()
	{
		return $this->cool_count;
	}

	public function set_comment_count($val)
	{
		$this->comment_count = $val;
	}
	public function get_comment_count()
	{
		return $this->comment_count;
	}

	public function set_user_image($val)
	{
		$this->user_image = $val;
	}
	public function get_user_image()
	{
		return $this->user_image;
	}


}
