<?php

class Phobio_UserRequest {
	var $username;
	var $password;
	var $email;
	var $first_name;
	var $last_name;
	var $company_location_uid;
	var $phone;
	var $client_reference;
	var $is_company_manager;
	var $external_uid;
	
	public function __construct($email,$first_name,$last_name,$company_location_uid) {
		$this->setEmail($email);
		$this->setFirstName($first_name);
		$this->setLastName($last_name);
		$this->setCompanyLocationUID($company_location_uid);
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function setFirstName($first_name) {
		$this->first_name = $first_name;
	}
	
	public function setLastName($last_name) {
		$this->last_name = $last_name;
	}
	
	public function setCompanyLocationUID($company_location_uid) {
		$this->company_location_uid = $company_location_uid;
	}
	
	public function setUsername($username) {
		$this->username = $username;
	}
		
	public function setPassword($password) {
		$this->password = $password;
	}
	
	public function setIsCompanyManager($is_manager=true) {
		$this->is_company_manager = ($is_manager)?1:0;
	}
	
	public function setPhone($phone) {
		$this->phone = preg_replace('/\D/','',$phone);
	}

	public function setClientReference($client_reference) {
		$this->client_reference = $client_reference;
	}
	
	public function setExternalUID($external_uid) {
		$this->external_uid = $external_uid;
	}
	
	public function toArray($for_request=true) {
		if ( $for_request && !$this->username && !$this->external_uid ) die('You must specify a username or external_uid');
		$array = (array)$this;
		foreach ( $array as $k => $v ) {
			if ( !isset($array[$k]) ) unset($array[$k]);			
		}
		return $array;
	}
		
	public function toJSON() {
		$object = $this;
		return json_encode($object);
	}
}