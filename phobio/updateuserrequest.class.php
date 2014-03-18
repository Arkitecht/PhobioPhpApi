<?php

class Phobio_UpdateUserRequest extends Phobio_UserRequest {
	var $is_active;
	
	public function __construct($username) {
		$this->setUsername($username);
	}
	
	public function setPassword($password) {
		return false;
	}
	
	public function setIsActive($is_active=true) {
		$this->is_active = ($is_active)?1:0;
	}
	
	public static function createFromUserObject($userObject) {
		$user = new Phobio_UpdateUserRequest($userObject->username);
		$userObjectAsArray= (array)$userObject;
		foreach ( $userObjectAsArray as $k => $v ) {
			if ( $k == 'username' ) continue;
			if ( $k == 'company_location' ) {	
				$user->setCompanyLocationUID($v->uid);
				continue;
			}
			if ( $k == 'is_active' ) {
				$user->setIsActive($v);
				continue;
			}
			$user->$k = $v;
		}
		return $user;
	}
	
}