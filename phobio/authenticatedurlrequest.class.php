<?php
class Phobio_AuthenticatedURLRequest {
	var $product_slug;
	var $company_location_uid;
	var $sku;
	var $customer_first_name;
	var $customer_last_name;
	var $customer_phone;
	var $customer_id_type;
	var $customer_id_num;
	
	public function __construct() {
		$this->skus 						= array();
		$this->customer_id_num 	= array();
		$this->customer_id_type = array();
	}
	
	public function setCompanyLocationUID($company_location_uid) {
		$this->company_location_uid = $company_location_uid;
	}

	public function setProductSlug($product_slug) {
		$this->product_slug = $product_slug;
	}
	
	public function setCustomerName($first_name='',$last_name='') {
		$this->customer_first_name = $first_name;
		$this->customer_last_name	 = $last_name; 
	}
	
	public function setCustomerPhone($phone) {
		$this->customer_phone = $phone;
	}
		
	public function addCustomerID($customer_id_num='',$customer_id_type='') {
		$this->customer_id_num[] 	= $customer_id_num;
		$this->customer_id_type[] = $customer_id_type;		
	}

	public function addSku($sku) {
		$this->sku[] = $sku;
	}

	public function toArray($for_request=true) {	
		$array = (array)$this;
		foreach ( $array as $k => $v ) {
			if ( !$array[$k] && !is_numeric($array[$k]) ) unset($array[$k]);			
		}
		return $array;
	}
}
?>