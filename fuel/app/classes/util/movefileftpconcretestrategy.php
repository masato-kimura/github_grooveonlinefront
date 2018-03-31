<?php
namespace util;

class MoveFileFtpConcreteStrategy implements MoveFileStrategy {

	private $connectId;
	private $remote_server;
	private $user;
	private $pass;

	public function __construct($remote_server, $user, $pass)
	{
		$this->remote_server = preg_replace('/[\/\s]+$/', '', $remote_server); // 後ろのスラッシュは無し
		$this->user = $user;
		$this->pass = $pass;

		try
		{
			$this->connectId = ftp_connect($this->remote_server);
			if ( ! ftp_login($this->connectId, $user, $pass))
			{
				throw new Exception('ftp_login_error');
			}
		}
		catch (\Exception $e)
		{
			return true;
		}
	}

	/*
	 * @return boolen
	 */
	public function chkdir($path) {
		return ftp_chdir($this->connectId, $path);
	}

    /*
     * @return dirName or false
     */
    public function mkdir($dirName) {
        return ftp_mkdir($this->connectId, $dirName);
    }

    /*
     * @return boolen
     */
    public function del($path) {
        return ftp_delete($this->connectId, $path);
    }

    /*
     * $from_path : 元ファイル
     * $to_path   : 転送先で新たに命名するファイル名
     */
    public function upload($from_path, $to_path, $fileType=FTP_BINARY)
    {
		\Log::debug('[start]'. __METHOD__);

		$to_path = preg_replace('/^[\/]/', '', $to_path);

		$url = 'ftp://'. $this->user. ':'. $this->pass. '@'. $this->remote_server. DS. $to_path;

		\Log::info('from_path: '. $from_path);
		\Log::info('to_path: '. $to_path);
		\Log::info('url: '. $url);

		$ch = curl_init();
		$fp = fopen($from_path, 'r');
    	curl_setopt($ch, CURLOPT_FTP_USE_EPSV, false);
    	curl_setopt($ch, CURLOPT_UPLOAD, true);
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_FTP_CREATE_MISSING_DIRS, true);
    	curl_setopt($ch, CURLOPT_INFILE, $fp);
    	curl_setopt($ch, CURLOPT_INFILESIZE, filesize($from_path));
    	curl_exec($ch);
    	$error_no = curl_errno($ch);
    	curl_close($ch);
    	fclose($fp);

    	\Log::debug('error_no : '. $error_no);
    	if ($error_no != 0)
    	{
    		return false;
    	}

    	return true;
    }
}