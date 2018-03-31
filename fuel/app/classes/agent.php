<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

//namespace Fuel\Core;

/**
 * Identifies the platform, browser, robot, or mobile device from the user agent string
 *
 * This class uses PHP's get_browser() to get details from the browsers user agent
 * string. If not available, it can use a coded alternative using the php_browscap.ini
 * file from http://browsers.garykeith.com.
 *
 * @package	    Fuel
 * @subpackage  Core
 * @category    Core
 * @author      Harro Verton
 */

class Agent extends Fuel\Core\Agent
{
	public static function _init()
	{
		parent::_init();

		$sp_list = array(
				'iPhone',
				'iPod',
				'Android',
				'IEMobile',
				'dream',
				'CUPCAKE',
				'blackberry9500',
				'blackberry9530',
				'blackberry9520',
				'blackberry9550',
				'blackberry9800',
				'webOS',
				'incognito',
				'webmate'
		);

		$pattern = '/'.implode('|', $sp_list).'/i';
		static::$properties['x_issmartphone'] = preg_match($pattern, static::$user_agent) ? true : false;
	}


	public static function is_smartphone()
	{
		return static::$properties['x_issmartphone'];
	}
}
