<?php
date_default_timezone_set('Europe/Kyiv');

/**
 * Array contains data for response forming according its status code
 */
define('RESPONSE_DATA', [
    '200' => ['statusMessage' => 'OK'],
    '400' => ['statusMessage' => 'Bad Request'],
    '403' => ['statusMessage' => 'Forbidden'],
    '404' => ['statusMessage' => 'Not Found'],
]);

/**
 * Array contains the corresponding base directories for different hosts
 */
define('HOST_DIRECTORY', [
    'student.shpp.me' => ['baseDirectory' => 'student'],
    'another.shpp.me' => ['baseDirectory' => 'another'],
]);

/**
 * Defaut base directory for unknown hosts
 */
define('DEFAULT_HOST_DIRECTORY', 'else');

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
 * Outputs HTTP responce
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
 * @param array     $body
 */
function processHttpRequest($method, $uri, $headers, $body) : void {
    $response = getResponse($method, $uri, $headers);
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
        $headers[trim(string: substr($strAsArr[$i], 0, $ind))] = trim(substr($strAsArr[$i], $ind+1));
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
 function getStrToParse($string) : string {
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
function hasBody($string, $arrLastItem) : bool {
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
function cleanArray($arr) : array {
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
 * @param array     $requestHeaders
 * 
 * @return array
 */
function getResponse($requestMethod, $requestUri, $requestHeaders) : array {
    try{
        checkRequestMethod($requestMethod);
        $baseDirectory = getBaseDirectoryByHost($requestHeaders);
        $filepath = getFilepath($baseDirectory, $requestUri);
        $fileName = basename($filepath);        
        
        if(!file_exists($filepath)) {
            throw new Exception('File <strong>' . $fileName . '</strong> not found', 404);
        }

        $fileText = file_get_contents($filepath);
        $statusCode = 200;
        $body = getResponseBody($statusCode, $fileText, $fileName);
    } catch(Exception $ex) {
        $statusCode = $ex->getCode();
        $bodyMessage = $ex->getMessage();
        $body = getResponseBody($statusCode, $bodyMessage);
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
function getResponseStatusMessage($statusCode) : string {
    return RESPONSE_DATA[$statusCode]['statusMessage'];
}

/**
 * @param int       $statusCode
 * @param string    $bodyMessage
 * @param string    $fileName
 * 
 * @return string
 */
function getResponseBody(int $statusCode, string $bodyMessage, string $fileName=null) : string {
    $body = '<h1>' . $statusCode . ' ' . RESPONSE_DATA[$statusCode]['statusMessage'] . '</h1>';
    $body .= PHP_EOL;
    $body .= $fileName ? '<h2>' . $fileName . '</h2>' : '';
    $body .= PHP_EOL;
    $body .= '<p>' . $bodyMessage . '</p>';

    return $body;
}

/**
 * @param array $headers
 * 
 * @throws Exception
 * @return string
 */
function getBaseDirectoryByHost(array $headers) : string {
    if(!isset($headers['Host'])) {
        throw new Exception('Header \'Host\' is absent', 400);
    }

    if(!isset(HOST_DIRECTORY[$headers['Host']]))
        return DEFAULT_HOST_DIRECTORY;

    return HOST_DIRECTORY[$headers['Host']]['baseDirectory'];
}

/**
 * @param string $baseDirectory
 * @param string $uri
 * 
 * @throws Exception
 * @return string
 */
function getFilepath(string $baseDirectory, string $uri) : string {
    if($uri === '/') $uri = '/index.html';

    // check if the $uri tries to go out of the base directory
    if(strpos($uri, '..')) {
        $filepath = $baseDirectory . $uri;

        while(preg_match('#(\w+/\.{2}/)#', $filepath)) {
            $filepath = preg_replace('#(\w+/\.{2}/)#', '', $filepath);
        }

        if(strpos($filepath, $baseDirectory) === FALSE) {
            throw new Exception('Access is denied', 403);
        }
    }

    return $baseDirectory . $uri;
}