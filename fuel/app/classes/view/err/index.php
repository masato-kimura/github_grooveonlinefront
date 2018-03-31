<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * The welcome 404 view model.
 *
 * @package  app
 * @extends  ViewModel
 */
class View_Err_Index extends ViewModel
{
	/**
	 * Prepare the view data, keeping this in here helps clean up
	 * the controller.
	 *
	 * @return void
	 */
	public function view()
	{
		if (empty($this->e))
		{
			$message = 'Error';
			$detail  = '現在システムエラーが発生しております。';
			$detail .= '<br />復旧作業をおこなっておりますので、いましばらくお待ち下さるよう何卒よろしくお願いいたします。';
		}
		else
		{
			switch ($this->e->getCode())
			{
				case 3001:
					$message = 'Network Error';
					$detail  = "アクセスいただきありがとうございます。ただいまネットワークが大変混み合っております。";
					$detail .= "<br />復旧までいましばらくお待ち下さるよう何卒よろしくお願いいたします。";
					break;

				default:
					$message = 'Error';
					$detail  = '現在システムエラーが発生しております。';
					$detail .= '<br />復旧作業をおこなっておりますので、いましばらくお待ち下さるよう何卒よろしくお願いいたします。';
			}
		}
		$this->title  = $message;
		$this->detail = $detail;
	}
}
