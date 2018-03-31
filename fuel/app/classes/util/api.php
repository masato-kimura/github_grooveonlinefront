<?php
namespace util;

use model\dto\CurlDto;
use Review\Model\Dto\CoolDto;
class Api
{
	private $url;
	private $header;
	private $arr_send = array();
	// curl関数自体の最大待ち時間
	private $timeout;
	// ネットワークコネクションのレスポンス最大待ち時間
	private $connection_timeout;
	// SSL認証を実行するか
	private $ssl_verifypeer;
	private $obj_response = null;

	public function __construct()
	{
		$curl_dto = CurlDto::get_instance();
		$this->header             = \Config::get('host.api_header');
		$this->url                = $curl_dto->get_url();
		$this->arr_send           = $curl_dto->get_arr_send();
		$this->timeout            = $curl_dto->get_timeout();
		if (is_null($this->timeout))
		{
			$this->timeout = 60; // 60秒
		}

		$this->connection_timeout = $curl_dto->get_connection_timeout();
		if (is_null($this->connection_timeout))
		{
			$this->connection_timeout = 60; // 60秒
		}

		$this->ssl_verifypeer     = $curl_dto->get_ssl_varifypeer();
		if (is_null($this->ssl_verifypeer))
		{
			$this->ssl_verifypeer = false;
		}
	}

	private function set_api_key()
	{
		$curl_dto = CurlDto::get_instance();
		$this->arr_send['api_key'] = $curl_dto->get_api_key();
		if (empty($this->arr_send['api_key']))
		{
			$this->arr_send['api_key'] = \Config::get('host.api_key');
		}
	}

	public function send_curl()
	{
		\Log::debug('[start]'. __METHOD__);

		$cool_dto = CoolDto::get_instance();

		$this->set_api_key();
		$json_profile = json_encode($this->arr_send);
		$ch = curl_init($this->url);
\Log::info($this->url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_profile);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer); // SSL証明書を検証しない
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connection_timeout);   // タイムアウト（秒）
		curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);

		$json_response = curl_exec($ch);
		curl_close($ch);

		$this->obj_response = json_decode($json_response);
		if (empty($this->obj_response))
		{
			throw new \Exception('APIからのレスポンスがありません', 3001); // network error
		}

		$is_success = $this->obj_response->success;
		if (isset($is_success) and $this->obj_response->success === false)
		{
			if ($this->obj_response->code == '7010')
			{
				throw new \Exception($this->obj_response->response, 7010); // ログインエラー
			}
			throw new \Exception($this->obj_response->response, 9001); // success false
		}

		return true;
	}


	public function get_curl_response()
	{
		return $this->obj_response;
	}
}
