<?php
namespace Fuel\Tasks;

use Fuel\Core\Cache;
use model\domain\service\InformationService;
use model\dto\InformationDto;
class Info
{
	/**
	 *
	 * @param string $about (track|album)
	 * @return boolean
	 */
	public static function get()
	{
		\Log::debug('[start]'. __METHOD__);

		InformationService::get_information();
		$information_dto = InformationDto::get_instance();
		Cache::set('information', $information_dto->get_arr_list());
		Cache::set('last_information_name', InformationService::get_last_information_name());

		return true;
	}
}