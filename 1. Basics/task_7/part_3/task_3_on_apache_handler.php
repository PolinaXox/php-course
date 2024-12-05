<?php
/**
 * Array contains data for response forming according its status code
 */
define('RESPONSE_DATA', [
    '200' => ['statusMessage' => 'OK'],
    '400' => ['statusMessage' => 'Bad Request'],
    '404' => ['statusMessage' => 'Not Found'],
]);

processHttpRequest();

// REQUEST PROCESS FUNCTION ///////////////////////////////////////////////////////////////////////
/**
 * Processes http request, creates and send the reaponse. 
 * Optionally outputs the results as HTML page.
 * 
 * @return void
 */
function processHttpRequest() : void {
	$nums = testInputValue($_GET['nums'] ?? false);
	$invalidRequest = checkRequestCorrectness($nums);
	$isHTML = preg_match('#html#', $_SERVER['HTTP_ACCEPT'] ?? '');
	
	if($invalidRequest) {
		http_response_code($invalidRequest['code']);
		header('Error-Info: ' . $invalidRequest['info']);
	}
	
	$responseBody = $invalidRequest ? $invalidRequest['info'] : array_sum(explode(',', $nums));	
	
	if($responseBody && !$isHTML) echo $responseBody;
	
	if($isHTML) outputHTML($responseBody);
}

/**
 * Checks the request's correctness.
 * If the request is correct return false.
 * If the request is incorrect returns corresponding response code and some additional information.
 *
 * @param string|bool $nums
 * @return bool|array
 */
function checkRequestCorrectness(string|bool $nums) : bool|array {
	$methodGet = (($_SERVER['REQUEST_METHOD'] ?? null) === 'GET') ? true : false;				// 400
	$correctUri = preg_match('#^/sum#', $_SERVER['REQUEST_URI'] ?? '');		// 404
	$correctNums = preg_match('#^(\d+,?)+$#', $nums);							// 400
	
	if(!$methodGet) return ['code' => 400, 'info' => 'Request method is invalid. The method GET is needed.'];

	if(!$correctUri) return ['code' => 404, 'info' => 'Not found. URI is invalid.'];

	if(!$nums) return ['code' => 400, 'info' => 'Value \'nums\' is absent.'];
	
	if(!$correctNums) return [ 'code' => 400, 'info' => 'Value \'nums\' is NOT comma separate numbers'];
	
	return false; // 200 OK

	// Option: without additional informaton about invalid request 
	// if($methodGet && $correctNums && $correctUri) return false;
	// return $correctUri ? 400 : 404;	
}

// HTML OUTPUT FUNCTIONS //////////////////////////////////////////////////////////////////////////

 function outputHTML(string $responseBody):void {

	// send response for right headers list view on the HTML page 
	flush();
	
	// title
	echo '<h1>Task 3:</h1>';
	
	// request
	echo '<h2>Request:</h2>';
	outputRequest();	
	echo '<hr style="border-top: 5px double #777">';
	
	// response
	echo '<h2>Response:</h2>';
	outputResponse($responseBody);
	echo '<hr style="border-top: 5px double #777">';

	// link 
	echo '<br>'. mb_chr(129092) .' <a href=' . $_SERVER['HTTP_REFERER'] .'> Back to the request page</a>';
}

/**
 * Output the user`s request uncluding some superglobals with some decorative elements
 * 
 * @return void
 */
function outputRequest() : void {
	
	// request line
	echo testInputValue(apache_lookup_uri($_SERVER['REQUEST_URI'])->the_request) . '<br>';

	// headers
	$headers = getallheaders();
	array_walk($headers, 'testInputValueByReference');
	array_walk($headers, 'printKeyAndValue');

	// body
	$body = testInputValue(file_get_contents('php://input'));
	if($body) echo '<br>' . $body;
}


/**
 * @param array $responseDraft
 * 
 * @return void
 */
function outputResponse($body) : void {
	$code = http_response_code();
	echo $_SERVER['SERVER_PROTOCOL'] . ' ' . $code . ' ' . getResponseStatusMessage($code) . '<br>';
	
	$headers = apache_response_headers();
	array_walk($headers, 'printKeyAndValue');
	echo '<br>';
	
	if($body) echo $body;
}

/**
 * @param int $statusCode
 * 
 * @return string
 */
function getResponseStatusMessage(int $statusCode) : string {
    return RESPONSE_DATA[$statusCode]['statusMessage'];
}


/**
 * Outputs key and its value in a nice separate line 
 * (Uses as callback function for array_walk() for print header's/variable's names and values)
 * 
 * @param string $value
 * @param string $key
 * 
 * @return void
 */
function printKeyAndValue(string &$value, string $key) : void {
	echo '<strong>' . $key . '</strong>: ' . $value . '<br>';
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
 function testInputValueByReference(string &$data) : void { //
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
 function testInputValue(string $data) : string {
	testInputValueByReference($data);
	return $data;
}