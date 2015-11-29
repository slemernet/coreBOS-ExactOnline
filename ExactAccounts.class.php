<?php
require('modules/ExactOnline/ExactApi.class.php');

class ExactAccounts extends ExactApi{
	
	public function listAll($division, $selection) {
		// Returns an array of all accounts with the account fields
		// Provided in the '$selection' (comma separated input)
		return $this->sendGetRequest('crm/Accounts', $division, $selection);
	}
	
	public function CreateAccount($division, $fields) {
		if ( is_array($fields) ) {
			// Don't accept an account that doesn't have a code
			if ( isset($fields['Code']) && $fields['Code'] != "" ) {
				// We should check if the account already exists first
				if ( $this->AccountExists($division, $fields) ) {
					echo "This account already exists";
				} else if ( !$this->AccountExists($division, $fields) ) {
					$this->sendPostRequest('crm/Accounts', $division, $fields);
				}
			} else {
				echo "You need to provide an Account code, make sure to set array key with a capital C.";
			}
		} else {
			echo "When using function 'CreateAccount', Post fields should be in array form";
		}
	}
	
	public function AccountExists($division, $fields) {
		// Start the codeExists value on FALSE
		$codeExists = 0;
		// Make sure we strip any non-numerical from the code we
		// want to check, since Exact will return only numbers also
		$fields['Code'] = preg_replace("/[^0-9,.]/", "", $fields['Code']);
		// First, GET all the accounts, this returns an array
		// To check if an account exists, we only need the code
		// Because we regard this as the unique ID
		$AccountsCodeArray = $this->listAll($division,'Code');
		// Now loop through the returned accounts
		foreach ( $AccountsCodeArray['feed']['entry'] as $entry ) {
			// Check every code from Exact and match it against the code we're feeding to
			// This method. Set a variable to true or false depending on if it's found
			// Make sure to TRIM the result from Exact, because it will be 18 characters
			// long filled with leading spaces
			$ExactAccountCode = trim($entry['content']['m:properties']['d:Code']);
			// var_dump($ExactAccountCode);
			// echo "<br>";
			// var_dump($fields['Code']);
			// echo "<br>";
			if ( $ExactAccountCode == $fields['Code'] ) {
				$codeExists = 1;
				// Stop the loop if it exists
				break;
			} else {
				$codeExists = 0;
			}
		}
		if ($codeExists == 1) {return TRUE;} else {return FALSE;}
	}
	
}

?>