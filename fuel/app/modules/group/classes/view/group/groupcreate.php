<?php

class View_Group_Groupcreate extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$this->name = \Input::post('name');
		$this->category_id = \Input::post('category_id');
		$this->category_name = \Input::post('category_name');
		$this->profile_fields = \Input::post('profile_fields');
		$this->error_image = empty($this->error_image) ? null : $this->error_image;
	}
}