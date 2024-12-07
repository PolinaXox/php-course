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
 * Processes http request, partly forms the response. 
 * Optionally outputs the results as HTML page.
 * 
 * @return void
 */
function processHttpRequest() : void {
	$nums = makeInputDataSafe($_GET['nums'] ?? false);
	$invalidRequest = checkRequestCorrectness($nums);
	
	if($invalidRequest) {
		http_response_code($invalidRequest['code']);
		header('Error-Info: ' . $invalidRequest['info']);
	}
	
	$responseBody = $invalidRequest ? ($invalidRequest['info'] ?? false) : array_sum(explode(',', $nums));	
	$isHTML = preg_match('#html#', $_SERVER['HTTP_ACCEPT'] ?? '');

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

	// 400
	$methodGet = (($_SERVER['REQUEST_METHOD'] ?? null) === 'GET') ? true : false;
	if(!$methodGet) return ['code' => 400, 'info' => 'Request method is invalid. The method GET is needed.'];

	// 400
	if(!$nums) return ['code' => 400, 'info' => 'Value \'nums\' is absent.'];

	// 404
	$correctUri = preg_match('#^/sum#', $_SERVER['REQUEST_URI'] ?? '');
	if(!$correctUri) return ['code' => 404, 'info' => 'Not found. URI is invalid.'];

	// 400
	$correctNums = preg_match('#^(\d+,?)+$#', $nums);
	if(!$correctNums) return [ 'code' => 400, 'info' => 'Value \'nums\' is NOT comma separate numbers'];
	
	// 200 OK
	return false; 	
}

// HTML OUTPUT FUNCTIONS //////////////////////////////////////////////////////////////////////////
 /**
  * @param string $responseBody
  * 
  * @return void
  */
 function outputHTML(string $responseBody) : void {

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
 * Output the user`s request uncluding some decorative elements
 * 
 * @return void
 */
function outputRequest() : void {
	
	// request line
	echo makeInputDataSafe(apache_lookup_uri($_SERVER['REQUEST_URI'])->the_request) . '<br>';

	// headers
	$headers = getallheaders();
	array_walk($headers, 'makeInputDataSafeByReference');
	array_walk($headers, 'printKeyAndValue');

	// body
	$body = makeInputDataSafe(file_get_contents('php://input'));
	if($body) echo '<br>' . $body;
}

/**
 * @param bool|string $body
 * 
 * @return void
 */
function outputResponse(bool|string $body) : void {
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