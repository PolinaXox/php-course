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
	try {
		validateRequest();
		/*checkRequestMethod();
        checkRequestUri();
        checkRequestContentTypeValue();
*/
		userAutorization();

		//sanitizeLogin();
		//$loginAndPasswordPair = validateLoginAndPassword();
		//validateUser($loginAndPasswordPair['login'], $loginAndPasswordPair['password']);
		echo '<h1 style=\'color:green\'>FOUND</h1>';
    } catch(Exception $ex) {
		http_response_code($ex->getCode());
        echo '<p>' . $ex->getMessage() . '</p>';
    };	
}

////////////////////////////////////////////////////////////s

function validateRequest() : void {
	checkRequestMethod();
	checkRequestUri();
	checkRequestContentTypeValue();
	checkAuthentication();
}

function checkAuthentication() : void {
	
	// authentification OK: all fields exist
	if(($_POST['login'] ?? false) && ($_POST['password'] ?? false)) {
		return;
	}

	throw new Exception('Authentification failed.', 400);
}


function userAutorization() : void {
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


// REQUEST PROCESS FUNCTIONS. VALIDATON FUNCTIONS /////////////////////////////////////////////////
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
 * @return array
 */
/*
function validateLoginAndPassword(): array {
	$login = sanitizeInput($_POST['login'] ?? false);

	// Field 'login' is absent or it's empty
	if($login == false) {
		throw new Exception('Something wrong with field \'login\'. Turn to the administrator.', 400);
	}

	// Field 'login' is empty
	//if(empty($login)) {
	//	throw new Exception('Login value is absent(empty field)', 404);
	//}

	$password = sanitizeInput($_POST['password'] ?? false);
	
	if($password === false) {
		throw new Exception('Password field is absent', 404);
	}
	
	if(empty($password)) {
		throw new Exception('Password value is absent(empty field)', 404);
	}

	return ['login' => $login, 'password' => $password,];
}
	*/

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

/**
 * @param string $login
 * @param string $password
 * @throws Exception
 * @return void
 */
/*
function validateUser(string $login, string $password) : void {
    
	// перевірити логін, що прийшов, на допустимість
	// перевірити пароль, що прийшов, на допустимість 

	// відкрити файл (мій файл ідеален)
	// зчитати рядок
	// розділити його на логін і пароль
	// перевірити логін на співпадіння 
	// перевірити пароль на співпадіння
	// якщо пароль і логін - ок -> закрити файл і return
	// якщо пароль і логін not ок -> закрити файл перед поверненням

	
	$logAndPassPairsAsArr = getFileContentAsPairsArray(FILE_NAME, PHP_EOL, ':');

    foreach($logAndPassPairsAsArr as $log => $pass) {
        if($log === $login and $pass === $password) {
			return;
		}
              
		if($log === $login) {
            throw new Exception('Password didn`t match. Fogot password?', 404);        
        }
    }

    throw new Exception('User not found. Would you like to register?', 404);
}
	*/

// FILES PROCESSING FUNCTIONS ////////////////////////////////////////////////////
/**
 * @param string $filename
 * @param string $delim1
 * @param string $delim2
 * 
 * @return array
 */
/*
function getFileContentAsPairsArray($filename, $delim1, $delim2) : array {
    return getStringContentAsPairsArray(file_get_contents($filename), $delim1, $delim2);
}
	*/

// STRINGS PROCESSING FUNCTIONS ////////////////////////////////////////////////////
/**
 * @param string $str
 * @param string $delim1
 * @param string $delim2
 * 
 * @return array
 */
/*
function getStringContentAsPairsArray($str, $delim1, $delim2) : array {
    $arr = array_filter(explode($delim1, $str));
    $pairsAsArr = [];
    
    foreach($arr as $pair) {
        $p = explode($delim2, $pair);
        $pairsAsArr += [trim($p[0])=>trim($p[1])];
    }
    
    return array_filter($pairsAsArr);
}
*/

// CHECK INPUT DATA FUNCTIONS //////////////////////////////////////////////////
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