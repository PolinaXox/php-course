<?php
date_default_timezone_set('Europe/Kyiv');

/**
 * Array contains data for response forming according its status code
 */
define('RESPONSE_DATA', [
    '200' => ['statusMessage' => 'OK'],
    '400' => ['statusMessage' => 'Bad Request'],
    '404' => ['statusMessage' => 'Not Found'],
]);

/**
 * Returns the input request example as a string
 * 
 * @return string 
 */
function readHttpLikeInput() : string {
    $f = fopen( 'php://stdin', 'r' );
    $store = "";
    $toread = 0;
   
    while( $line = fgets( $f ) ) {
        $store .= preg_replace("/\r/", "", $line);
        
        if (preg_match('/Content-Length: (\d+)/',$line,$m))
            $toread=$m[1]*1; 
        
        if ($line == "\r\n")
            break;
    }
    
    if ($toread > 0) 
        $store .= fread($f, $toread);

    return $store;
}

$contents = readHttpLikeInput();

/**
 * Outputs HTTP response
 * 
 * @param int       $statuscode
 * @param string    $statusmessage
 * @param array     $headers
 * @param string    $body
 */
function outputHttpResponse($statuscode, $statusmessage, $headers, $body) : void {
    echo "HTTP/1.1 $statuscode $statusmessage" . PHP_EOL; 

    foreach($headers as $header => $value) {         
        echo "$header : $value" . PHP_EOL;
    }

    echo PHP_EOL . $body . PHP_EOL; 
}

/**
 * Processes the request, creates and outputs the response
 * 
 * @param string    $method
 * @param string    $uri
 * @param array     $headers
 * @param string    $body
 */
function processHttpRequest($method, $uri, $headers, $body) : void {
    $response = getResponse($method, $uri);
    outputHttpResponse($response['statusCode'], $response['statusMessage'], $response['headers'], $response['body']); 
}

/**
 * Gets input request sample as a string and returns array of the needed request parts
 * 
 * @param string $string
 *
 * @return array
 */
function parseTcpStringAsHttpRequest($string) : array {
    $strToParse = getStrToParse($string);
    $strAsArr = cleanArray(explode(PHP_EOL, $strToParse));
    
    // parse and delete method and uri
    $methodAndURI = explode(" ", array_shift($strAsArr));
    
    // find body if it is and delete body from the array
    $body = "";
    $arrSize = count($strAsArr);
    
    if($arrSize > 0) {
        if(hasBody($string, $strAsArr[$arrSize-1])) {
            $body = array_pop($strAsArr);
            $arrSize--;
        }
    }

    // parse headers[]
    $headers = [];
    $i = 0;
    
    // if the request has headers store each header data as
    // $headers[headerName] = headerValue
    while($i < $arrSize) {
        $ind = strpos($strAsArr[$i], ":");
        $headers[trim(substr($strAsArr[$i], 0, $ind))] = trim(substr($strAsArr[$i], $ind+1));
        $i++;
    }
    
    return [
        "method" => trim($methodAndURI[0]),
        "uri" => trim($methodAndURI[1]),
        "headers" => $headers,
        "body" => $body,
    ];
}

$http = parseTcpStringAsHttpRequest($contents);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);

//REQUEST PROCESSNG FUNCTIONS///////////////////////////////////////////////////////////

/**
 * Gets string as it input and returns a string which let use PHP_EOL as a delimeter
 * 
 * @param string $string
 * 
 * @return string
 */
 function getStrToParse(string $string) : string {
    $str = preg_replace("/\r/", "", $string);
    $str = preg_replace("/\n/", PHP_EOL, $str);

    return $str;
}

/**
 * @param string $string
 * @param string $arrLastItem
 * 
 * @return bool
 */
function hasBody(string $string, string $arrLastItem) : bool {
    return preg_match('/Content-Length:/', $string) || !preg_match('/:/', $arrLastItem);
}

//ARRAYS PROCESSNG FUNCTIONS////////////////////////////////////////////////////////////

/**
 * Removes items which are empty lines from the array
 * 
 * @param array $arr
 * 
 * @return array
 */
function cleanArray(array $arr) : array {
    $resultArr = [];
    
    foreach($arr as $item) {
        if(strlen($item) > 0){
            $resultArr[]=$item;
        }
    }

    return $resultArr;
}

// RESPONSE MAKING FUNCTIONS///////////////////////////////////////////////////////////

/**
 * Forms the server response depends on the client request
 * 
 * @param string    $requestMethod
 * @param string    $requestUri
 * 
 * @return array
 */
function getResponse(string $requestMethod, string $requestUri) : array {
    try {
        $statusCode = getResponseStatusCode($requestMethod, $requestUri);   
        $body = getResponseBody($requestUri); 
    } catch(Exception $ex) {
        $statusCode = $ex->getCode();
        $body = $ex->getMessage();
    };

    $statusMessage = getResponseStatusMessage($statusCode);
    
    $headers = [
        "Date" => date(DATE_RFC1123),                           
        "Server" => "Apache/2.2.14 (Win32)",                
        "Content-Length" => strlen($body), 
        "Connection" => "Closed",          
        "Content-Type" => "text/html; charset=utf-8",       
    ];
        
    return [
        'statusCode' => $statusCode,
        'statusMessage' => $statusMessage,
        'headers' => $headers,
        'body' => $body,
    ];
}

/**
 * @param string $method
 * 
 * @throws Exception
 * @return void
 */
function checkRequestMethod(string $method) : void {
    if( $method === "GET") return;

    throw new Exception("Method is invalid. The method GET is needed.", 400);
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
 * @param string $method
 * @param string $uri
 * 
 * @throws Exception
 * 
 * @return int
 */
function getResponseStatusCode(string $method, string $uri) : int {
    if($method != "GET") {
        throw new Exception("Method is invalid. The method GET is needed.", 400);
    }

    if(!preg_match('/\?nums=/i', $uri)) {
        throw new Exception("Uri is invalid.", 400);
    }
    
    if(!preg_match('#^/sum\?nums=[\d,]+#i', $uri)) { 
        throw new Exception("not found", 404);
    }

    return 200;
}


/**
 * @param string    $uri
 * 
 * @return string
 */
 function getResponseBody(string $uri) : string {
    $ind = strpos($uri, "=");
    $numsArr = explode(",", substr($uri, $ind+1));  
    
    return array_sum($numsArr);
}