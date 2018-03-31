<?php
namespace util;

class MoveFileLocalConcreteStrategy implements MoveFileStrategy {

    public function __construct() {

    }

    /*
     * @return boolen
    */
    public function chkdir($path) {
        return file_exists($path);
    }

    /*
     * @return dirName or false
    */
    public function mkdir($dirName) {
        if (mkdir($dirName)) {
            return true;
        } else {
            throw new Exception('mkdir_error');
        }
    }

    /*
     * @return true or Exception
    */
    public function del($path) {
        if (unlink($path)) {
            return true;
        } else {
            throw new Exception('del_error');
        }
    }

    /*
     * $from : 元ファイル
     * $to : 転送先で新たに命名するファイル名
     * @return true or Exception
     */
	public function upload($from_path, $to_path, $file_type=null)
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$new_dir = preg_replace('/\/[^\/]+$/', '/', $to_path);
		if ( ! file_exists($new_dir))
		{
			mkdir($new_dir, 0777, true);
		}

		if ( ! copy($from_path, $to_path))
		{
			throw new Exception("There was a problem while uploading". $from_path);
		}

		return true;
	}
}