<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Member_Model extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	
	function register($input){
		$query=$this->db->insert('memberDetails', $input);
		if($query){
			return "OK|Thankyou, IPN has been successfully been saved.";
		}
	}
	
	function random_string($length = 4) {
		$firstPart="M".substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"),0,2);
		
		// Generate random 4 character string
		$string = md5(microtime());
		$secondPart = substr($string,1,$length);
		$randomString = $firstPart.strtoupper($secondPart);
	
		//Confirm its not a duplicate
		$this->db->where('registrationCode',$randomString);
		$query=$this->db->get('memberDetails');
		if($query->num_rows()>0){
			random_string($length);
		}else{
			return $randomString;
		}
	}
	
}