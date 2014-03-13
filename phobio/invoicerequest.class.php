<?php

class Phobio_InvoiceRequest {
	var $quote_uid;
	var $agree_to_terms;
	var $credit_option_uid;
	var $reference;
	var $customer_first_name;
	var $customer_last_name;
	var $customer_id_num;
	var $customer_id_type;
	var $customer_alt_id_num;
	var $customer_alt_id_type;	
	var $company_location_uid;
	var $sales_sku;
	
	public function __construct($quote_uid,$agree_to_terms=true,$credit_option_uid='') {
		$this->quote_uid 						= $quote_uid;
		$this->agree_to_terms 			= $agree_to_terms;
		$this->credit_option_uid		= $credit_option_uid;
		$this->sales_sku						= array();
	}
		
	public function addSalesSku($sku) {
		$this->sales_sku[] = $sku;
	}
	
	public function setCustomer($first_name,$last_name='') {
		$this->customer_first_name = $first_name;
		$this->customer_last_name = $last_name;
	}
	
	public function setCustomerID($id_num,$id_type) {
		$this->customer_id_num  = $id_num;
		$this->customer_id_type = $id_type;
	}

	public function setCustomerAltID($id_num,$id_type) {
		$this->customer_alt_id_num  = $id_num;
		$this->customer_alt_id_type = $id_type;
	}
		
	public function setCompanyLocationUID($company_location_uid) {
		$this->company_location_uid = $company_location_uid;
	}
	
	public function toArray() {
		$array = (array)$this;
		foreach ( $array as $k => $v ) {
			if ( !$array[$k] ) unset($array[$k]);			
		}
		return $array;
	}
		
	public function toJSON() {
		return json_encode($this);
	}
}