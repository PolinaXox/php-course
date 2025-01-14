<?php
/**
 * File to search logins and passwords
 */
define(constant_name: 'FILE_NAME', value: 'passwords.txt');

processHttpRequest();

// REQUEST PROCESS FUNCTIONS //////////////////////////////////////////////////////////////////////

/**
 * @return void
 */
function processHttpRequest() : void {
	try {
		validateRequest();
		userAuthorization();
		echo '<h1 style=\'color:green\'>FOUND</h1>';
    } catch(Exception $ex) {
		http_response_code($ex->getCode());
        echo '<p>' . $ex->getMessage() . '</p>';
    };	
}

// REQUEST PROCESS FUNCTIONS. VALIDATON FUNCTIONS /////////////////////////////////////////////////

/**
 * @return void
 */
function validateRequest() : void {
	checkRequestMethod();
	checkRequestUri();
	checkRequestContentTypeValue();
	checkAuthentication();
}

/**
 * @throws Exception
 * @return void
 */
function checkRequestMethod() : void {
	if(($_SERVER['REQUEST_METHOD'] ?? null) === 'POST') {
		return;
	}

    throw new Exception('Request method is invalid. The method POST is needed.', 400);
}

/**
 * @throws Exception
 * @return void
 */
function checkRequestUri() : void {		
	if(preg_match('#^/api/checkLoginAndPassword$#', $_SERVER['REQUEST_URI'] ?? '')) {
		return;
	}

    throw new Exception('URI is invalid.', 404);
}

/**
 * @throws Exception
 * @return void
 */
function checkRequestContentTypeValue() : void {
   	if(preg_match('#\bapplication/x-www-form-urlencoded\b#i', apache_request_headers()['Content-Type'] ?? '')) {
		return;
	}
    
    throw new Exception('Header\'s \'Content-Type\' value is invalid.', 400);
}

/**
 * @throws Exception
 * @return void
 */
function checkAuthentication() : void {
	
	// authentification OK: all fields exist
	if(($_POST['login'] ?? false) && ($_POST['password'] ?? false)) {
		return;
	}

	throw new Exception('Authentification failed.', 400);
}

// REQUEST PROCESS FUNCTIONS. AUTHORIZATION FUNCTIONS ////////////////////////////////////////////////////////

/**
 * @throws Exception
 * @return void
 */
function userAuthorization() : void {
	checkFileExistance(FILE_NAME);
	$file = fopen(FILE_NAME, 'r');
	$login = sanitizeInput($_POST['login'] ?? false);
	
	while (!feof($file)) {
		preg_match('#^(.*):(.*)'. PHP_EOL .'$#', fgets($file), $logAndPass);

		// feof or login didn't match
		if(empty($logAndPass) || $logAndPass[1] !== $login) {
			continue;
		}

		fclose($file);
		
		// log - ok, pass - ok
		if($logAndPass[2] === sanitizeInput($_POST['password'] ?? false)) {
			return;
		}

		// log - ok, pass - NOT ok
		throw new Exception('Authorization failed. Fogot password?', 401);
	}

	// login not found
	fclose($file);
	throw new Exception('User is not found. Would you like to register?', 404);
}

// SERVER CHECKING FUNCTIONS //////////////////////////////////////////////////////////////////////

/**
 * @param string $fileName
 * @throws Exception
 * @return void
 */
function checkFileExistance(string $fileName) : void {
    if(file_exists($fileName)) {
		return;
	}
    
    throw new Exception('File ' . $fileName . ' doesn`t exist', 500);
}

// CHECK INPUT DATA FUNCTIONS /////////////////////////////////////////////////////////////////////

/**
 * Changes input data to prevent harm, injections ect.
 * (Able to use as a callback function for array_walk() and for array_walk_recursive())
 * 
 * @param string $data
 * 
 * @return void
 */
 function sanitizeInputByReference(string &$data) : void {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
}

/**
 * Changes input data to prevent harm, injections ect.
 * 
 * @param string $data
 * 
 * @return string
 */
 function sanitizeInput(string $data) : string {
	sanitizeInputByReference($data);
	
	return $data;
}
