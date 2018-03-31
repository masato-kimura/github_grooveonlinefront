<?php
use Fuel\Core\Fuel;
/**
 * ログクラス
 *
 * @package	MPFS
 * @since	PHP5.4.16
 * @version	1.0
 * @final
 */
final class Log extends \Fuel\Core\Log
{
	/**
	 * config file name [config/mpfs.php]
	 * @var string mpfs
	 * @static
	 */
	private static $_config_key		= "";
	private static $_arr_messages	= array();
	private static $_session_keyword = '';

	/**
	 * Initialize the class
	 */
	public static function _init()
	{
		// 定義keyを取得
		self::$_config_key = self::_get_config_key();

		//self::$_session_keyword = date('Y-m-d H:i:s')."-".rand(1000,9999);
		self::$_session_keyword = rand(1000,9999);

		// determine the name and location of the logfile
		$rootpath		= \Config::get(self::$_config_key.".log.path");
		$_file_format	= \Config::get(self::$_config_key.".log.file_format");
		$_file_ext		= \Config::get(self::$_config_key.".log.ext");

		// 定義がなければデフォルトのパスに出力
		if ($rootpath == "" || $_file_format == "" || $_file_ext == "")
		{
			return parent::_init();
		}

		$filename = $rootpath.date($_file_format).$_file_ext;

		$_arr_mail_setting = \Config::get(self::$_config_key.".mail");

		// make sure the log directories exist
		try
		{
			// get the required folder permissions
			$permission = \Config::get('file.chmod.folders', 0777);

			if ( ! is_dir($rootpath))
			{
				mkdir($rootpath, 0777, true);
				chmod($rootpath, $permission);
			}
			$handle = fopen($filename, 'a');
		}
		catch (\Exception $e)
		{
			\Config::set('log_threshold', \Fuel::L_NONE);
			throw new \FuelException('Unable to create or write to the log file. Please check the permissions on '.\Config::get('log_path'));
		}

		if ( ! filesize($filename))
		{
			fwrite($handle, "<?php defined('COREPATH') or exit('No direct script access allowed'); ?>".PHP_EOL.PHP_EOL);
			chmod($filename, \Config::get('file.chmod.files', 0666));
		}
		fclose($handle);

		// create the monolog instance
		static::$monolog = new \Monolog\Logger('fuelphp');

		$format = \Config::get('log_level_output') ? "[%datetime%]\t%message%" : "[%datetime%]\t[%level_name%]\t%message%";

		// create the streamhandler, and activate the handler
		$stream = new \Monolog\Handler\StreamHandler($filename, \Monolog\Logger::DEBUG);
		$formatter = new \Monolog\Formatter\LineFormatter($format.PHP_EOL, "Y-m-d H:i:s");
		$stream->setFormatter($formatter);
		static::$monolog->pushHandler($stream);
	}

	/**
	 * specific config key string
	 * @access private
	 * @static
	 * @return string
	 */
	private static function _get_config_key()
	{
		$_action_name = '';
		$_class_name = '';
		// controller
		if (\Input::server("SCRIPT_NAME") == "/index.php") {
			$_obj_uri = new \Uri();
			$_segments = $_obj_uri->get_segments();
			$_action_name = strtolower(array_pop($_segments));
			$_class_name = strtolower(array_pop($_segments));
			if ( ! empty($_segments))
			{
				if ($_segments[0] === 'api')
				{
					return 'api';
				}
			}
		}
		// tasks
		else
		{
			$_oil_param = \Cli::option(2);

			if ( strpos($_oil_param,":") === false )
			{
				$_class_name  = $_oil_param;
			}
			else
			{
				list($_class_name,$_action_name) = explode(":",\Cli::option("2"));
			}
			if ( empty($_action_name) ) $_action_name = "run";
		}
		return $_class_name.".".$_action_name;
	}

	/**
	 * Logs a message with the Error Log Level
	 * @access	public
	 * @param	mixed	$msg	The log message
	 * @param	string	$method	The method that logged
	 * @return	boolean	If it was successfully logged
	 */
	public static function error($msg,$method = null)
	{
		if ($msg instanceof \Exception)
		{
			$message = get_class($msg).' : '.$msg->getMessage();
			$message.= ($msg->getCode()!==0) ? ' '.$msg->getCode() : '';
			$message.= ' ['.$msg->getFile().' :'.$msg->getLine().']';
			return static::error($message);
		}
		if ( ! is_scalar($msg))
		{
			$msg = print_r($msg,true);
		}
		return parent::error("[ERROR] ".$msg,$method);
	}

	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @access	public
	 * @param	int|string	the error level
	 * @param	string	the error message
	 * @param	string	information about the method
	 * @return	bool
	 */
	public static function write($level, $msg, $method = null)
	{
		if ( ! is_scalar($msg)) $msg = print_r($msg,true);

		$msg = self::$_session_keyword.": ".$msg;

		// 出力しなかった場合、false
		if ( !parent::write($level, $msg,$method) )
		{
			return false;
		}
		self::$_arr_messages[] = $msg;
		return true;
	}

	/**
	 * send eMail
	 * @access	public
	 * @static
	 * @return	boolean
	 */
	public static function send()
	{
		$subject = \Config::get(self::$_config_key.".mail.subject");
		$from    = \Config::get(self::$_config_key.".mail.from");
		$to      = \Config::get(self::$_config_key.".mail.to");
		$return  = \Config::get(self::$_config_key.".mail.return");

		if ($subject == "")	$subject = \Config::get("mpfs.mail.subject");
		if ($from    == "")	$from    = \Config::get("mpfs.mail.from");
		if ($to      == "")	$to      = \Config::get("mpfs.mail.to");
		if ($return  == "")	$return  = \Config::get("mpfs.mail.return");

		$obj_email = \Email::forge();
		// subject
		$obj_email->subject($subject);
		// from
		$obj_email->from($from);
		// to
		$arr_to = explode(",",$to);
		foreach ($arr_to as $mailto)
		{
			$obj_email->to($mailto);
		}
		// return
		$obj_email->return_path($return);
		// body
		$body = "";
		if ( ! empty(self::$_arr_messages))
		{
			$body = implode("\r\n",self::$_arr_messages);
		}
		$obj_email->body($body);

		try
		{
			\Log::debug("send mail: ".$subject);
			\Log::info("from: ".$from);
			\Log::info("to: ".$to);
			\Log::info("return: ".$return);

			$obj_email->send();
		}
		catch (\EmailValidationFailedException $e)
		{
			\Log::error("EmailValidationFailedException occurred: ".$e->getMessage());
			throw new \FuelException($e->getMessage());
		}
		catch (\EmailSendingFailedException $e)
		{
			\Log::error("EmailSendingFailedException occurred: ".$e->getMessage());
			throw new \FuelException($e->getMessage());
		}
		return true;
	}
}