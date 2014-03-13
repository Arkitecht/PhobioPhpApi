<?php

define('PHOBIO_DEFAULT_PER_PAGE',30);

require_once(dirname(__FILE__).'/phobio/quoterequest.class.php');
require_once(dirname(__FILE__).'/phobio/invoicerequest.class.php');

class Phobio {
	var $username;
	var $password;
	var $token;
	var $staging;
	var $lang;
	
	public function __construct($username,$token,$staging=false,$lang='en') {
		$this->username = $username;
		$this->token    = $token;
		$this->staging  = ($staging===true)?true:false;
		$this->setLanguage($lang);		
	}
	
	public function setLanguage($lang) {
		$langs = array('en','de','en-gb','zh-cn');
		if ( !in_array($lang, $langs) ) die("$lang is not in list of allowed languages");
		$this->lang = $lang;
	}
	
	public function getManufacturers($per_page=PHOBIO_DEFAULT_PER_PAGE,$page=1) {
		return $this->_makeAuthenticatedRequest('manufacturers/',array('per_page'=>$per_page,'page'=>$page));
	}
	
	public function getManufacturer($manufacturer_slug) {
		return $this->_makeAuthenticatedRequest("manufacturers/$manufacturer_slug");
	}
	
	public function getManufacturerProducts($manufacturer_slug,$per_page=PHOBIO_DEFAULT_PER_PAGE,$page=1) {
		return $this->_makeAuthenticatedRequest("manufacturers/$manufacturer_slug/products/",array('per_page'=>$per_page,'page'=>$page));
	}	
	
	public function getProductSearch($query,$per_page=PHOBIO_DEFAULT_PER_PAGE,$page=1) {
		return $this->_makeAuthenticatedRequest("products/search/",array('q'=>$query,'per_page'=>$per_page,'page'=>$page));
	}
	
	public function getProduct($product_slug) {
		return $this->_makeAuthenticatedRequest("products/$product_slug/");
	}
	
	public function createQuote(Phobio_QuoteRequest $quote) {
		return $this->_makeAuthenticatedRequest("quotes/",array('_raw'=>$quote->toJSON()),'POST');
	}
	
	public function getQuote($quote_uid) {
		return $this->_makeAuthenticatedRequest("quotes/$quote_uid");
	}
	
	public function confirmQuote($quote_uid,$agree_to_terms) {
		return $this->_makeAuthenticatedRequest("invoices/",array('quote_uid'=>$quote_uid,'agree_to_terms'=>true),'POST');
	}
	
	public function createInvoice(Phobio_InvoiceRequest $invoice) {
		return $this->_makeAuthenticatedRequest("invoices/",$invoice->toArray(),'POST');
	}	
	
	public function getInvoices() {
		return $this->_makeAuthenticatedRequest("invoices");
	}
		
	public function getInvoice($invoice_uid) {
		return $this->_makeAuthenticatedRequest("invoices/$invoice_uid/");
	}
	
	public function deleteInvoice($invoice_uid) {
		return $this->_makeAuthenticatedRequest("invoices/$invoice_uid/",'','DELETE');
	}
	
	public function createShipment($invoice_uid) {
		return $this->_makeAuthenticatedRequest("shipments/",array('invoices'=>$invoice_uid),'POST');
	}
	
	public function getShipment($shipment_id) {
		return $this->_makeAuthenticatedRequest("shipments/$shipment_id");
	}
	
	public function getTermsAndConditions() {
		return $this->_makeAuthenticatedRequest("terms/");
	}
	
	public function getAuthenticatedURL($web_app_slug,$params='') {
		$slugs = array('embedded_trade_flow','tools','dashboards_and_analytics','trade_reports','shipments','device_erasures','shipping_supplies');
		if ( !in_array($web_app_slug,$slugs) ) die("$web_app_slug is not in list of recognized apps");
		return $this->_makeAuthenticatedRequest("urls/$web_app_slug/",$params);
	}
	
	private function _makeAuthenticatedRequest($resource,$params=array(),$method='GET',$headers=array(),$returnRawResponse=false) {
		$url = ( $this->staging ) ? 'http://staging.phobio.com' : 'http://phobio.com';
	
		$full_url = $url . "/api/$resource";
	
		$default_headers = array(
			'Accept' => 'application/json',
			'Accept-Language' => $this->lang,
			"X-API-Authorization" => "token=$this->token;username=$this->username"
		);
		
		if ( $method == 'GET' || $params['_raw'] ) $default_headers['Content-Type'] = 'application/json';
		
		$all_headers = array_merge($default_headers,$headers);
		
		$headers = array();
		foreach ( $all_headers as $header_name => $header_val ) {
			$headers[] = "$header_name: $header_val";
		}
		
		$ch = curl_init();
		
		if ( $method == 'GET' ) {
			if ( $params ) $full_url = $full_url.'?'.http_build_query_flat($params);
		} elseif ( $method == 'POST' ) {
			curl_setopt($ch, CURLOPT_POST, true );
			if ( $params ) {
				if ( $params['_raw'] ) $params = $params['_raw'];
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
			}
		} else {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method );
			if ( $params ) curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
		}
		
		curl_setopt($ch, CURLOPT_URL, $full_url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		if ( $this->debug ) {
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true );
		}
		
		$response = curl_exec($ch);
		
		if ( $this->debug ) {
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			
			$body = substr($response, $header_size);
			
			$response = $body;
		}
		
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if ( $http_code == 401 ) {
			$response = json_encode(array('error_code'=>401,'error'=>'Unauthorized Access'));
		}
			
		if ( $this->debug ) {		
			print_r(curl_getinfo($ch));
			print $header;
			print $response;
		}
		
		if ( $returnRawResponse ) return $response;
		else return json_decode($response);
	}
	
	public function __toString() {
  	$pw = $this->token;
  	$this->token = '***************';
  	$string = print_r($this,1);
  	$this->token = $pw;
  	return $string;
  }
	
}

function http_build_query_flat($params) {
	$query = http_build_query($params);
	$query = preg_replace('/\%5B[0-9]+\%5D/', '', $query );
	return $query;
}
?>