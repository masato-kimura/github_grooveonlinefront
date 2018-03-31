<?php
namespace util;

use Fuel\Core\Upload;
class Image {

	protected $save_image_dir_path = null;
	protected $arr_ext_white_list = array('jpg', 'jpeg', 'gif', 'png');
	protected $arr_uploaded_image_info = array();
	protected $arr_modified_image_info = array();

	private $srcX = null;
	private $srcY = null;
	private $image_error = null;

	private $config = array();

	public function __construct()
	{
		# default save directory
		$this->save_image_dir_path = DOCROOT. 'assets'. DS. 'img'. DS. 'tmp'. DS;
		$this->arr_ext_white_list = array('jpg', 'jpeg', 'gif', 'png');
	}

	/**
	 * OSの一時ディレクトリにアップロードされたファイルを名前をつけて指定の場所に移動し保存する
	 * また画像情報をメンバ変数へセットする
	 * (default_dir : ~/public/assets/img/tmp/)
	 *
	 * @param string $save_image_dir_path
	 * @param string $prefix 保存するファイル名の接頭辞
	 * @return boolean
	 */
	public function get_uploaded_tmp_image($save_image_dir_path=null, $prefix=null)
	{
		\Log::debug('[start]'. __METHOD__);

		if ( ! empty($save_image_dir_path))
		{
			$save_image_dir_path = preg_replace('/[\/\s]$/', '', $save_image_dir_path);
			$save_image_dir_path = $save_image_dir_path. DS;
		}
		else
		{
			$save_image_dir_path = $this->save_image_dir_path;
		}

		$this->config = array(
				'path' 		  => $save_image_dir_path,
				'ext_whitelist' => $this->arr_ext_white_list,
				'auto_rename'   => false,
				'overwrite'     => true,
				'prefix'        => $prefix. "_",
				'max_size'      => \Config::get('image.user_image_max_upload_size'), // 6Mbyte
		);

		Upload::process($this->config);
		if (Upload::is_valid())
		{
			Upload::save();

			# 保存された画像ファイルを取得
			$arr_files = Upload::get_files();

			# 保存された画像ファイル情報をメンバ変数にセット
			$image_file_path = $save_image_dir_path. $prefix. "_". $arr_files[0]['name'];
			$arr_image_info = getimagesize($image_file_path);
			$this->arr_uploaded_image_info['path']   = $image_file_path;
			$this->arr_uploaded_image_info['width']  = $arr_image_info[0];
			$this->arr_uploaded_image_info['height'] = $arr_image_info[1];
			$this->arr_uploaded_image_info['type']   = $arr_image_info[2];

			return true;
		}
		else
		{
			if ($arr_error = \Upload::get_errors())
			{
				$this->image_error = $arr_error[0]['errors'][0]['error'];

				if ($arr_error[0]['errors'][0]['error'] == '4')
				{
					\Log::info($arr_error[0]['errors'][0]['message']. '['. $arr_error[0]['errors'][0]['error']. ']');
					\Log::info(__FILE__. '['. __LINE__.']');
					return true;
				}
				\Log::error($arr_error[0]['errors'][0]['message']. '['. $arr_error[0]['errors'][0]['error']. ']');
				\Log::error(__FILE__. '['. __LINE__.']');
				//throw new \Exception($arr_error[0]['errors'][0]['message']. '['. $arr_error[0]['errors'][0]['error']. ']');
			}
			else
			{
				\Log::error('画像アップロードでエラーが発生しました');
				\Log::error(__FILE__. '['. __LINE__.']');
				//throw new \Exception('画像アップロードでエラーが発生しました');
			}

			return false;
		}
	}

	/**
	 * アップロードしたファイル情報を取得
	 * @return multitype:
	 */
	public function get_uploaded_image_info()
	{
		return $this->arr_uploaded_image_info;
	}

	public function get_otherside_size($from_width, $from_height, $to_size, $from_side='width')
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		if ($from_side === 'width')
		{
			$iPer =  $from_width / $to_size;
			$to_other_size = round($from_height / $iPer);
			return array($to_size, $to_other_size);
		}
		else
		{
			$iPer =  $from_height / $to_size;
			$to_other_size = round($from_width / $iPer);
			return array($to_other_size, $to_size);
		}
	}

	public function modify_image($from_file_path, $to_file_path, $width, $height, $image_type='jpg', $position=0, $unlink=true)
	{
		\Log::debug('[start]'. __METHOD__);

		if ( ! function_exists('imagecreatefromjpeg'))
		{
			throw new Exception('GDがインストールされてません');
		}

		$to_dir_path   = preg_replace('/\/[^\/]+$/', '/', $to_file_path);
		$from_dir_path = preg_replace('/\/[^\/]+$/', '/', $from_file_path);

		$arr_from_image_info =  getimagesize($from_file_path);
		$from_image_width  = $arr_from_image_info[0];
		$from_image_height = $arr_from_image_info[1];
		$from_image_type   = $arr_from_image_info[2];

		$obj_move_file_concrete_strategy = new MoveFileLocalConcreteStrategy();
		$obj_move_file_context = new MoveFileContext($obj_move_file_concrete_strategy);

		# 画像処理後の一時ファイルを格納するディレクトリの存在確認と作成
		if ( ! $obj_move_file_context->chkdir($to_dir_path))
		{
			\Log::info("kotti");
			$obj_move_file_context->mkdir($to_dir_path);
		}

		# 対象サイズ位置調整
		if ($from_image_width > $from_image_height)
		{
			$this->srcX = (($from_image_width - $from_image_height) / 2) - $position;
			if ($this->srcX < 0)
			{
				$this->srcX = 0;
			}
			elseif ($this->srcX > ($from_image_width - $from_image_height))
			{
				$this->srcX = $from_image_width - $from_image_height;
			}
			$this->srcY = 0;
			$src_size = $from_image_height;
		}
		else if ($from_image_width < $from_image_height)
		{
			$this->srcX = 0;
			$this->srcY = (($from_image_height - $from_image_width) / 2) - $position;
			if ($this->srcY < 0)
			{
				$this->srcY = 0;
			}
			elseif ($this->srcY > ($from_image_height - $from_image_width))
			{
				$this->srcY = $from_image_height - $from_image_width;
			}
			$src_size = $from_image_width;
		}
		else
		{
			$this->srcX = 0;
			$this->srcY = 0;
			$src_size = $from_image_width;
		}

		# 元画像のリソースを取得
		$resourse_origin_img = $this->_get_image_resource($from_file_path, $from_image_type);

		# パレットを用意
		$img_base = imagecreatetruecolor($width, $height);

		# パレットに元画像リソースを貼り付ける
		imagecopyresampled($img_base, $resourse_origin_img, 0, 0, $this->srcX, $this->srcY, $width, $height, $src_size, $src_size);

		# パレットをもとにし生成された画像リソースからJPG画像を生成し指定のパスへ配置する
		imagejpeg($img_base, $to_file_path, 100);

		# パレットリソースを削除
		imagedestroy($img_base);

		return true;
	}


	public static function url_exists($url)
	{
		\Log::debug('[start]'. __METHOD__);
		$hdrs = @get_headers(preg_replace('/^https/i', 'http', $url));
		return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false;
	}


	public function get_error()
	{
		return $this->image_error;
	}


	// 画像を回転
	private function _image_rotate($image, $angle, $bgd_color){
		return imagerotate($image, $angle, $bgd_color, 0);
	}


	private function _get_image_resource($image_file_path, $image_type)
	{
		\Log::debug('[start]'. __METHOD__);

		switch ($image_type)
		{
			case IMAGETYPE_JPEG:
				$resourse_img = imagecreatefromjpeg($image_file_path);
				if (function_exists('exif_read_data'))
				{
					$exif_data = exif_read_data($image_file_path);
					if (isset($exif_data['Orientation']) && ($exif_data['Orientation'] == '6'))
					{
						$resourse_img = $this->_image_rotate($resourse_img, 270, 0);
						$srcX = $this->srcX;
						$srcY = $this->srcY;
						$this->srcX = $srcY;
						$this->srcY = $srcX;
					}
				}
				break;
			case IMAGETYPE_GIF:
				$resourse_img = imagecreatefromgif($image_file_path);
				break;
			case IMAGETYPE_PNG:
				$resourse_img = imagecreatefrompng($image_file_path);
				break;
			default:
				throw new \Exception('有効な画像リソースを取得できませんでした。path=>'. $image_file_path. ', image_type=>'. $image_type);
		}

		return $resourse_img;
	}

}

