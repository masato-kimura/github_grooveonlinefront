<?php
use Fuel\Core\Validation;
class AddValidation
{
	public static function _validation_check_to_segment_from_artist_search($to)
	{
		\Log::debug('[start]'. __METHOD__);

		if (empty($to))
		{
			return true;
		}

		$arr_to_params = array('review');
		if ( ! in_array($to, $arr_to_params))
		{
			return false;
		}

		return true;
	}


	public static function _validation_valid_reserve_name($name)
	{
		\Log::debug('[start]'. __METHOD__);

		$message = "『{$name}』は使用できません";
		if (method_exists(Validation::active(), 'set_message'))
		{
			Validation::active()->set_message('valid_reserve_name', $message);
		}

		if (preg_match('/(groove.*on|grooveon|group.*on|グルーブオ|グルーヴオ|グルーヴォ|ｸﾞﾙｰｳﾞｵ|ｸﾞﾙｰﾌﾞｵ|ｸﾞﾙｰｳﾞｫ)/i', $name))
		{
			return false;
		}

		return true;
	}
}