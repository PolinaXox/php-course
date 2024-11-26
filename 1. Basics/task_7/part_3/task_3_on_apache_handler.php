<?php
/**
 * Array contains data for response forming according its status code
 */
define('RESPONSE_DATA', [
    '200' => ['statusMessage' => 'OK'],
    '400' => ['statusMessage' => 'Bad Request'],
    '404' => ['statusMessage' => 'Not Found'],
]);

$request = getRequest();
processHttpRequest($request);


// REQEST GETTING FUNCTIONS ///////////////////////////////////////////////////////////////////////
/**
 * Gets, checks and return the real parts of user`s request
 * 
 * @return array 
 */
function getRequest() : array {
	
	// form an array corresponding to the request and delete empty items
	$requestAsArray = array_filter ( 
		[
			'requestLine' => apache_lookup_uri($_SERVER['REQUEST_URI'])->the_request,
			'method' => $_SERVER['REQUEST_METHOD'] ?? false,
			'uri' => $_SERVER['REQUEST_URI'] ?? false,
			'headers' => getallheaders(),
			'body' => file_get_contents('php://input'),
			'nums' => $_GET['nums'] ?? false,
			]
		);
	
	// check the array to prevent harm
	array_walk_recursive($requestAsArray, 'testInputValueByReference');
	
	return $requestAsArray;
}

// RESPONSE MAKING FUNCTIONS///////////////////////////////////////////////////////////

/**
 * Processes the request, creates and outputs the response
 * 
 * @param array $request
 * 
 * @return void
 */
 function processHttpRequest(array $request) : void {
	$responseDraft = getResponseDraft($request['method'], $request['uri'], $request['nums'] ?? false);
	setResponseHeaders($responseDraft);
	output($request, $responseDraft);
}

/**
 * Forms the server response depends on the client request
 * 
 * @param string $requestMethod
 * @param string $requestUri
 * @param string|bool $nums
 * 
 * @return array
 */
function getResponseDraft(string $requestMethod, string $requestUri, string|bool $nums) : array {
    try {
        $statusCode = getResponseStatusCode($requestMethod, $requestUri, $nums);   
        $body = getResponseBody($nums); 
    } catch(Exception $ex) {
        $statusCode = $ex->getCode();
        $body = $ex->getMessage();
    };

    return [
        'statusCode' => $statusCode,
        'statusMessage' => getResponseStatusMessage($statusCode),
        'body' => $body,
    ];
}

/**
 * @param string $method
 * @param string $uri
 * @param string|bool $nums
 * 
 * @throws Exception
 * 
 * @return int
 */
function getResponseStatusCode(string $method, string $uri, string|bool $nums) : int {
	if($method != 'GET') {
        throw new Exception('Method is invalid. The method GET is needed.', 400);
    }
    
	if(!preg_match('#^/sum#', $uri)) { 
        throw new Exception('not found', 404);
    }

	if(!$nums) {
        throw new Exception('Value \'nums\' is absent.', 400);
    }

	if(!preg_match('#^(\d+,?)+$#', $nums)) { 
        throw new Exception('Value \'nums\' is NOT comma separate numbers', 400);
    }

    return 200;
}

/**
 * Returns the sum of nums which comma separated
 * 
 * @param string $nums
 * 
 * @return string
 */
function getResponseBody(string $nums) : string {
	$numsArr = explode(",", $nums);
    return array_sum($numsArr);
}

/**
 * @param int $statusCode
 * 
 * @return string
 */
// ++
function getResponseStatusMessage(int $statusCode) : string {
    return RESPONSE_DATA[$statusCode]['statusMessage'];
}

/**
 * @param array $responseDraft
 * 
 * @return void
 */
function setResponseHeaders(array $responseDraft): void {
	header_remove();
	header(getResponseStatusLine($responseDraft));
	header('Date: ' . date(DATE_RFC1123));
	header('Server: ' . apache_get_version());
	header('Content-Length: ' . strlen($responseDraft['body']));
	header('Connection: close');
	header('Content-Type: text/html');
}

/**
 * @param array $responseDraft
 * 
 * @return string
 */
function getResponseStatusLine(array $responseDraft) : string {
	return $_SERVER['SERVER_PROTOCOL'] . ' ' . $responseDraft['statusCode'] . ' ' . $responseDraft['statusMessage'];
}


// OUTPUT FUNCTIONS ///////////////////////////////////////////////////////////////////////////////
/**
 * @param array $request
 * @param array $responseDraft
 * 
 * @return void
 */
function output(array $request, array $responseDraft):void {
	
	// title
	echo '<h1>Task 3:</h1>';
	
	// request
	echo '<h2>Request:</h2>';
	outputRequest($request);	
	echo '<hr style="border-top: 5px double #777">';
	
	// response
	echo '<h2>Response:</h2>';
	outputResponse($responseDraft);
	echo '<hr style="border-top: 5px double #777">';

	// link 
	echo '<br>'. mb_chr(129092) .' <a href=' . $_SERVER['HTTP_REFERER'] .'> Back to the request page</a>';
}

/**
 * Output the user`s request uncluding some superglobals with some decorative elements
 *  
 * @param array $userRequest
 * 
 * @return void
 */
function outputRequest(array $userRequest) : void {
	// request line
	echo $userRequest['requestLine'] . '<br>';

	// headers: header 'Host' only
	$headersNeeded = ['Host'];
	$fun = fn($key) : bool => in_array($key, $headersNeeded);
	$headers = array_filter($userRequest['headers'], $fun, ARRAY_FILTER_USE_KEY);
	array_walk($headers, 'printKeyAndValue');

	// body
	if(isset($userRequest['requestBody'])) echo '<br>' . $userRequest['requestBody'];

	// Superglobal`s content
	if(!empty($_GET)) printSuperglobal($_GET, '$_GET');

	if(!empty($_POST)) printSuperglobal($_POST, '$_POST');
}

/**
 * @param array $responseDraft
 * 
 * @return void
 */
function outputResponse(array $responseDraft) : void {
	echo getResponseStatusLine($responseDraft) . '<br>';
	$headers = makeAssociativeHeadersArray(headers_list());
	array_walk($headers, 'printKeyAndValue');
	echo '<br>';
	echo $responseDraft['body'];
}

/**
 * Turns indexes array into associative array for nice output
 * 
 * @param array $arr
 * 
 * @return array
 */
function makeAssociativeHeadersArray(array $arr): array {
	$result = [];
	
	foreach($arr as $arrItem){
		preg_match('/(^[^:]+):(.*)/', $arrItem, $matches);
		$result[$matches[1]] = $matches[2];
	}
	
	return $result;
}

/**
 *  Output the superglobal with some decorative elements
 * 
 * @param array $array
 * @param string $label
 * 
 * @return void
 */
function printSuperglobal(array &$array, string $label) : void {
	
	// prevent harm before output
	$arr = $array;
	array_walk_recursive($arr, 'testInputValueByReference');

	// output
	echo '<hr style="border-top: 1px dashed #333">';
	echo '<h3>Superglobal ' . $label . ' contains:</h3>';
	array_walk($arr, 'printKeyAndValue');
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
};

// CHECK INPUT DATA FUNCTIONS //////////////////////////////////////////////////
/**
 * Changes input data to prevent harm, injections ect.
 * (Uses as callback function for array_walk_recursive() for the input data)
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
