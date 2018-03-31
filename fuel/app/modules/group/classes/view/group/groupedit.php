<?php

use group\model\dto\GroupDto;
use group\domain\service\GroupService;
use user\model\dto\UserDto;
class View_Group_Groupedit extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __CLASS__. '::'. __FUNCTION__);

		$group_dto = GroupDto::get_instance();
		$user_dto = UserDto::get_instance();

		$this->user_id = $user_dto->get_user_id();
		$this->group_id = $group_dto->get_group_id();
		$this->group_name = $group_dto->get_name();
		$this->category_id = $group_dto->get_category_id();
		$this->category_name = $group_dto->get_category_name();
		$this->profile_fields = $group_dto->get_profile_fields();
		$this->group_image =GroupService::get_image_profile_url($group_dto->get_group_id(), true);
		$this->error_image = empty($this->error_image) ? null : $this->error_image;
		$this->members = $group_dto->get_members();
	}
}