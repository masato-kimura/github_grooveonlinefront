<?php
namespace model\domain\service;

use model\dto\CurlDto;
use util\Api;
use model\domain\service\Service;
use model\dto\SitemapDto;
use Fuel\Core\Format;

final class SitemapService extends Service
{
	public static function get_sitemap_from_api()
	{
		\Log::debug('[start]'. __METHOD__);

		$sitemap_dto = SitemapDto::get_instance();
		$url = \Config::get('host.api_url'). 'main/sitemap/get.json';
		$arr_send = array();

		// CURL送信のためDTOにセット
		$curl_dto = CurlDto::get_instance();
		$curl_dto->set_url($url);
		$curl_dto->set_arr_send($arr_send);

		// CURLでAPI送信
		$obj_api = new Api();
		$obj_api->send_curl();

		// CURL送信レスポンス
		$obj_response = $obj_api->get_curl_response();
		if ( ! property_exists($obj_response, 'result'))
		{
			throw new \Exception('apiレスポンスが不正です');
		}

		$sitemap_dto->set_arr_list($obj_response->result->arr_list);

		return true;
	}


	public static function set_sitemap_to_xml()
	{
		\Log::info('[start]'. __METHOD__);

		$sitemap_dto = SitemapDto::get_instance();
		$arr_list = $sitemap_dto->get_arr_list();
		$all_xml = '';
		foreach ($arr_list as $i => $val)
		{
			$arr_list[$i]->url->lastmod = \Date::forge(strtotime($val->url->lastmod))->format('%Y-%m-%d');
			$xml = Format::forge()->to_xml($val);
			$xml = preg_replace('/(<xml>|<\/xml>|<\?xml.+\?>)/i', '', $xml);
			$xml = preg_replace('/'.PHP_EOL.'$/', '', $xml);
			$all_xml = $all_xml. $xml;
		}
		$all_xml = preg_replace('/^'. PHP_EOL. '/', '', $all_xml);

		$information = \Cache::get('information');
		$information_date = current($information)->date;
		$information_date = \Date::forge(strtotime($information_date))->format('%Y-%m-%d');

		$default_xml  = '<url><loc>http://groove-online.com/</loc><lastmod>'. \Date::forge()->format("%Y-%m-%d").'</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>'. PHP_EOL;
		$default_xml .= '<url><loc>http://groove-online.com/artist/search/</loc><lastmod>'. \Date::forge()->format("%Y-%m-%d").'</lastmod><changefreq>daily</changefreq><priority>0.5</priority></url>'. PHP_EOL;
		$default_xml .= '<url><loc>http://groove-online.com/review/music/</loc><lastmod>'. \Date::forge()->format("%Y-%m-%d").'</lastmod><changefreq>daily</changefreq><priority>0.6</priority></url>'. PHP_EOL;
		$default_xml .= '<url><loc>https://groove-online.com/login</loc><lastmod>2015-12-25</lastmod><changefreq>never</changefreq><priority>0.3</priority></url>'. PHP_EOL;
		$default_xml .= '<url><loc>http://groove-online.com/index/aboutus/</loc><lastmod>2015-12-25</lastmod><changefreq>never</changefreq><priority>0.3</priority></url>'. PHP_EOL;
		$default_xml .= '<url><loc>https://groove-online.com/info/1/</loc><lastmod>'. $information_date.'</lastmod><changefreq>daily</changefreq><priority>0.9</priority></url>'. PHP_EOL;

		$all_xml = $default_xml. $all_xml;
		$out = '<?xml version="1.0" encoding="UTF-8"?>'. PHP_EOL. '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'. PHP_EOL. $all_xml. PHP_EOL. '</urlset>';
		\File::update(DOCROOT. 'public', 'sitemap.xml', $out);

		return true;
	}

}
