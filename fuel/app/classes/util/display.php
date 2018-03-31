<?php
namespace util;

class Display
{

	public function __construct()
	{
	}


	public static function loading($id='')
	{
		\Log::debug('[start]'. __METHOD__);

		$loading  = '<div id="'. $id.'" class="loading">'. PHP_EOL;
		$loading .= '	<span class="loading_disp" >Loading</span><br />'. PHP_EOL;
		$loading .= '	<span class="loading_disp l-1"></span>'. PHP_EOL;
		$loading .= '	<span class="loading_disp l-2"></span>'. PHP_EOL;
		$loading .= '	<span class="loading_disp l-3"></span>'. PHP_EOL;
		$loading .= '	<span class="loading_disp l-4"></span>'. PHP_EOL;
		$loading .= '	<span class="loading_disp l-5"></span>'. PHP_EOL;
		$loading .= '	<span class="loading_disp l-6"></span>'. PHP_EOL;
		$loading .= '</div>'. PHP_EOL;

		return $loading;
	}
}
