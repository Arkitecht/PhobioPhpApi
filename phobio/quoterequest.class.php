<?php

class Phobio_QuoteRequest {
	var $products;
	var $sales;
	var $promo_code;
	var $company_location_uid;
	private $productCnt;
	
	public function __construct($company_location_uid='',$promo_code='') {
		$this->products 						= array();
		$this->sales 								= array();
		$this->company_location_uid = $company_location_uid;
		$this->promo_code 					= $promo_code;
		$this->productCnt = 0;
	}
	
	public function addProduct($product_slug,$condition,$imei='',$qty='') {
		$product['slug'] 			= $product_slug;
		$product['condition'] = $condition;
		if ( $imei ) $product['imei'] = $imei;
		if ( $qty)   $product['qty']  = $qty;
		$this->products[$this->productCnt] = $product;
		$cnt = $this->productCnt;
		$this->productCnt++;
		return $cnt;
	}
	
	public function addSale($sku,$description) {
		$sale['sku'] 					= $sku;
		$sale['description'] 	= $description;
		$this->sales[] = $sale;
	}
	
	public function setPromoCode($code) {
		$this->promo_code = $code;
	}
	
	public function setCompanyLocationUID($company_location_uid) {
		$this->company_location_uid = $company_location_uid;
	}
	
	public function toJSON() {
		$object = $this;
		unset($object->productCnt);
		if ( !$object->promo_code ) unset($object->promo_code);
		if ( !$object->company_location_uid ) unset($object->company_location_uid);
		return json_encode($object);
	}
}