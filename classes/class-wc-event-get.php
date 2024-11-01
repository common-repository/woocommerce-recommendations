<?php
/* This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * WooCommerce Account setting manager
 *
 * Table of content
 *
 * nt_fetch_chart()
 * nt_fetch_recommendation()
 *
 */
 class Event_Get{
 	public $curl;

	public $userId;
	public $productId;

	protected $urlApi = "https://webapp1.ntoklo.com";
	protected $key;
	protected $secret;
	protected $nt_key_secret;

 	/**
	 * Constructor
	 */
	public function __construct(){
		$this->curl = new Curl();
		$this->key = get_option('key');
		$this->secret = get_option('secret');
		$this->general_settings = (array) get_option( $this->general_settings_key );

		$post_key = (array) get_option( 'general_settings' );
		$this->nt_key_secret = json_decode($post_key['key_secret']);

	}

	/**
	 * Load the class
	 */
	public function load() {
		//uncomment below for debugging
		//echo $this->nt_fetch_recommendation();
		//echo $this->nt_fetch_chart();
	}

	public function nt_fetch_chart($max_items, $tw) {

		$hmac_key 	= $this->nt_key_secret->key .  "&" . $this->nt_key_secret->secret;
		$httpGet 	= 'GET&' . $this->urlApi . '/chart?tw=' . $tw . '&maxItems=' . $max_items;

		$signature 	= hash_hmac('sha1', $httpGet, $hmac_key);
		$header_key = 'Authorization: NTOKLO ' . $this->nt_key_secret->key;

		$url = $this->urlApi . '/chart';
		$this->curl->setopt(CURLOPT_RETURNTRANSFER, TRUE);
		$this->curl->setHeader($header_key, $signature);
		$this->curl->get($url, array(
							'tw' => $tw,
							'maxItems' => $max_items
						));

		$response = json_decode($this->curl->response);

		if ($this->curl->error) {
   	 		return $this->curl->error_code;
		}else {
    		return $response;
		}
	}

	public function nt_fetch_recommendation($userId, $productId, $scope, $value) {

		$quaryParam = array(
			'userId' => $userId,
			'productId' => $productId,
			'scope' => $scope,
			'value' => $value
		);

		$queryParam = array_filter($quaryParam);
		$queryString = http_build_query($queryParam);

		$hmac_key 	= $this->nt_key_secret->key .  "&" . $this->nt_key_secret->secret;
		$httpGet 	= 'GET&' . $this->urlApi . '/recommendation?' . $queryString;
		$signature 	= hash_hmac('sha1', $httpGet, $hmac_key);
		$header_key = 'Authorization: NTOKLO ' . $this->nt_key_secret->key;
		$url = $this->urlApi . '/recommendation';
		$this->curl->setopt(CURLOPT_RETURNTRANSFER, TRUE);
		$this->curl->setHeader($header_key, $signature);
		$this->curl->get($url, $queryParam);

		$response = json_decode($this->curl->response);

		if ($this->curl->error) {
   	 		return $this->curl->error_code;
		}else {
    		return $response;
		}
	}
}//End of class
?>