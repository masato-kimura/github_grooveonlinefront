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
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Err extends \Controller_Gol_Template
{
	public function before()
	{
		parent::before();
	}

	public function action_index()
	{
		try
		{
			\Log::debug('--------------------------------------');
			\Log::debug('[start]'. __METHOD__);

			$this->template->content = ViewModel::forge('err/index', 'view', null, $this->device. '/err/index');
			$this->template->segment = 'err';

		}
		catch (\Exception $e)
		{
			var_dump($e->getMessage());
			exit;
		}
	}

	/**
	 * The 404 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_404()
	{
		\Response::forge(\View::forge($this->device. '/err/404'), 404);
//		$obj_view_model = ViewModel::forge('err/404', 'view', null, $this->device. '/err/404');
//		$this->template->content = $obj_view_model;
	}
}