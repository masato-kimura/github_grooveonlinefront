<?php

use group\domain\service\GroupService;
class View_Group_Groupeditconfirm extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);
		$this->group_id = \Input::post('group_id');
		$this->name = \Input::post('name');
		$this->category_id = \Input::post('category_id');
		$this->category_name = \Input::post('category_name');
		$this->profile_fields = \Input::post('profile_fields');
		$this->category = "";
		$this->picture_url = GroupService::get_image_profile_url($this->group_id, true);
		foreach ($this->obj_category as $val)
		{
			if ($val->id == \Input::post('category_id'))
			{
				$this->category = $val->name;
				break;
			}
		}
	}
}