<?php

class View_Music_Confirm extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$login_dto = \login\model\dto\LoginDto::get_instance();

		$this->artist_name	= \Input::post('artist_name');
		$this->album_name	= \Input::post('album_name');
		$this->music_name	= \Input::post('music_name');
		$this->link			= \Input::post('link');
		$this->review		= \Input::post('review');
		$this->about		= \Input::post('about');
		$this->star			= \Input::post('star');

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);
	}
}