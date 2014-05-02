<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
// Receiving messages boils down to reading values in the POST array
// This example will read in the values received and compose a response.

// 1.Import the helper Gateway class
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/AfricasTalkingGateway.php';
class Sms extends REST_Controller {
	var $phoneNumber = "";
	
	function __construct() {
		parent::__construct ();
		date_default_timezone_set ( 'Africa/Nairobi' );
		$this->load->library ( 'curl' );
		$this->load->library ( 'CoreScripts' );
		$this->load->model ( 'Member_Model', 'members' );
	}
	function custSms_post() {
		// 2.Read in the received values
		$phoneNumber = $this->post("from"); // sender's Phone Number
		$shortCode = $this->post ( "to" ); // The short code that received the message
		$text = $this->post ( "text" ); // Message text
		$linkId = $this->post ( "linkId" ); // Used To bill the user for the response
		$date = $this->post ( "date" ); // The time we received the message
		$id = $this->post ( "id" ); // A unique id for this message
		
		$phoneNumber = "0" . substr ( $phoneNumber, 4 );
		
		// 1. If Text is Blank && phoneNumber exist
		if ($text == "mridder") {
			
			
			$this->phoneNumber=$phoneNumber;
			
			$smsMessage = "Thanks for showing interest.Reply starting with INVESTOR," . "full names,IDNumber,".
						   "gender and county separated by comma." . "Example: Investor,Joe Njeri,12345,Male,Muranga";
			$this->sendToCustomer($smsMessage);
		} else {
			$this->doRegistration ($text,$phoneNumber);
		}
	}
	function validateFeedback($smsFeedback) {
		if ($smsFeedback) {
			echo "Success";
		} else {
			$message = "Muranga Sms Registration. This customer failed to receive SMS::".$this->phoneNumber;
			$this->corescripts->_send_sms2 ( "254729472421", $message );
		}
	}
	
	function doRegistration($text,$phoneNumber) {
		$this->phoneNumber=$phoneNumber;
		
		$custInput = explode ( ',', $text );
		
		if (sizeof($custInput) < 5) {
			$smsMessage = "Incorrect Format: Reply starting with INVESTOR,full names,IDNumber,".
						  "gender and county separated by comma." . "Example: Investor,Joe Njeri,12345,Male,Kiharo";
			
			$this->sendToCustomer($smsMessage);
			return;
		}
		
		// Ensure No Null Entry to Database
		$errorMessage = "Registration Unsuccessful. You have not filled in:";
		$hasError = false;
		if ($custInput [1] == "") {
			$errorMessage .= "Your FullNames,";
			$hasError = true;
		} elseif ($custInput [2] == "") {
			$errorMessage .= "Your Id Number,";
			$hasError = true;
		} elseif ($custInput [3] == "") {
			$errorMessage .= "Your Gender";
			$hasError = true;
		} elseif ($custInput [4] == "") {
			$errorMessage .= "Your SubCounty:e.g.Kiharo";
			$hasError = true;
		}
		
		$cust = array (
				'registrationCode' => $this->members->random_string ( 6 ),
				'registrationDate' => date("Y-m-d H:i:s"),
				'fullNames' => $custInput [1],
				'idNumber' => $custInput [2],
				'gender' => $custInput [3],
				'subCounty' => $custInput [4] 
		);
		
		if (!$hasError) {
			print_r ( $cust );
			$this->members->register($cust);
			
			$message = "You have successfully registered.Your Membership number is ".$cust['registrationCode'].
						".Use paybill number 318150 to invest a minimum of KES 35 per day or KES 1000 per month.";
			$this->sendToCustomer($message);
			
		} else {
			$this->sendToCustomer($errorMessage);
		}
	}
	function sendToCustomer($message) {
		$smsFeedback=$this->corescripts->_send_sms2 ($this->phoneNumber, $message );
		$this->validateFeedback($smsFeedback);
	}
}