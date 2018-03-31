<?php

use model\dto\InformationDto;
class View_Index_Info extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);
		$information_dto = InformationDto::get_instance();
		$this->arr_information = $information_dto->get_arr_list();
	}
}
