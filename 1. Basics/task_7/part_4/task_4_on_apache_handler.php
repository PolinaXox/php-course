<?php
/**
 * File to search logins and passwords
 */
define(constant_name: 'FILE_NAME', value: 'passwords.txt');

processHttpRequest();

// REQUEST PROCESS FUNCTIONS ///////////////////////////////////////////////////////////////////////
/**
 * @return void
 */
function processHttpRequest() : void {
	try{
        checkRequestMethod();
        checkRequestUri();
        checkRequestContentTypeValue();

		$loginAndPasswordPair = validateLoginAndPassword();
        checkFileExistance(FILE_NAME);

		validateUser($loginAndPasswordPair['login'], $loginAndPasswordPair['password']);
		echo '<h1 style=\'color:green\'>FOUND</h1>';
    }
    catch(Exception $ex) {
		http_response_code($ex->getCode());
        echo '<p>' . $ex->getMessage() . '</p>';
    };	
}

// REQUEST PROCESS FUNCTIONS. VALIDATON FUNCTIONS /////////////////////////////////////////////////
/**
 * @throws Exception
 * @return void
 */
function checkRequestMethod() : void {
	$methodPost = (($_SERVER['REQUEST_METHOD'] ?? null) === 'POST') ? true : false;

	if($methodPost) return;

    throw new Exception('Request method is invalid. The method POST is needed.', 400);
}

/**
 * @throws Exception
 * @return void
 */
function checkRequestUri() : void {
	$correctUri = preg_match('#^/api/checkLoginAndPassword$#', $_SERVER['REQUEST_URI'] ?? '');		
    
	if($correctUri) return;

    throw new Exception('URI is invalid.', 404);
}

/**
 * @throws Exception
 * @return void
 */
function checkRequestContentTypeValue() : void {
	$headerContentType = apache_request_headers()['Content-Type'] ?? '';
	$correctContentType = preg_match('#\bapplication/x-www-form-urlencoded\b#i', $headerContentType);

   	if($correctContentType) return;
    
    throw new Exception('Header\'s \'Content-Type\' value is invalid.', 400);
}

/**
 * @throws Exception
 * @return array
 */
function validateLoginAndPassword(): array {
	$login = makeInputDataSafe($_POST['login'] ?? false);

	if($login === false){
		throw new Exception('Login field is absent', 404);
	}

	if(empty($login)){
		throw new Exception('Login value is absent(empty field)', 404);
	}

	$password = makeInputDataSafe($_POST['password'] ?? false);
	
	if($password === false){
		throw new Exception('Password field is absent', 404);
	}
	
	if(empty($password)){
		throw new Exception('Password value is absent(empty field)', 404);
	}

	return ['login' => $login, 'password' => $password,];
}

/**
 * @param string $fileName
 * @throws Exception
 * @return void
 */
function checkFileExistance(string $fileName) : void {
    if(file_exists($fileName)) return;
    
    throw new Exception('File ' . $fileName . ' doesn`t exist', 500);
}

/**
 * @param string $login
 * @param string $password
 * @throws Exception
 * @return void
 */
function validateUser(string $login, string $password) : void {
    $logAndPassPairsAsArr = getFileContentAsPairsArray(FILE_NAME, PHP_EOL, ':');

    foreach($logAndPassPairsAsArr as $log => $pass) {
        if($log === $login and $pass === $password) return;
              
		if($log === $login) {
            throw new Exception('Password didn`t match. Fogot password?', 404);        
        }
    }

    throw new Exception('User not found. Would you like to register?', 404);
}


// FILES PROCESSING FUNCTIONS ////////////////////////////////////////////////////
/**
 * @param string $filename
 * @param string $delim1
 * @param string $delim2
 * 
 * @return array
 */
function getFileContentAsPairsArray($filename, $delim1, $delim2) : array {
    return getStringContentAsPairsArray(file_get_contents($filename), $delim1, $delim2);
}

// STRINGS PROCESSING FUNCTIONS ////////////////////////////////////////////////////
/**
 * @param string $str
 * @param string $delim1
 * @param string $delim2
 * 
 * @return array
 */
function getStringContentAsPairsArray($str, $delim1, $delim2) : array {
    $arr = array_filter(explode($delim1, $str));
    $pairsAsArr = [];
    
    foreach($arr as $pair) {
        $p = explode($delim2, $pair);
        $pairsAsArr += [trim($p[0])=>trim($p[1])];
    }
    
    return array_filter($pairsAsArr);
}

// CHECK INPUT DATA FUNCTIONS //////////////////////////////////////////////////
/**
 * Changes input data to prevent harm, injections ect.
 * (Able to use as a callback function for array_walk() and for array_walk_recursive())
 * 
 * @param string $data
 * 
 * @return void
 */
 function makeInputDataSafeByReference(string &$data) : void { //
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
 function makeInputDataSafe(string $data) : string {
	makeInputDataSafeByReference($data);
	return $data;
}