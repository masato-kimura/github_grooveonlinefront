<?php
namespace Fuel\Tasks;

use Fuel\Core\Cache;
use model\domain\service\InformationService;
use model\dto\InformationDto;
use model\domain\service\SitemapService;
class Sitemap
{
	/**
	 *
	 * @param string $about (track|album)
	 * @return boolean
	 */
	public static function get()
	{
		\Log::debug('[start]'. __METHOD__);

		SitemapService::get_sitemap_from_api();

		SitemapService::set_sitemap_to_xml();


		return true;
	}
}