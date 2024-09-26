<?php
/**
 * File to search logins and passwords
 */
define(constant_name: "FILE_NAME", value: "passwords.txt");

/**
 * Array contains data for response forming according its status code
 */
define("RESPONSE_DATA", [
    "200" => ["statusMessage" => "OK", "bodyMessage" => "FOUND", "color" => "green"],
    "400" => ["statusMessage" => "Bad Request", "color" => "red"],
    "404" => ["statusMessage" => "Not Found"],
    "500" => ["statusMessage" => "Internal Server Error", "color" => "blue"],
]);

date_default_timezone_set("Europe/Kyiv");

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
 * Processes the request and outputs the response
 * 
 * @param string    $method
 * @param string    $uri
 * @param array     $headers
 * @param array     $body
 */
function processHttpRequest($method, $uri, $headers, $body) : void {
    $statusCode = getResponseStatusCode($method, $uri, $headers["Content-Type"], $body);
    $statusMessage = getResponseStatusMessage($statusCode);
    $responseBody = getResponseBody($statusCode);

    $headers = [
        "Date" => date(DATE_RFC1123),                           
        "Server" => "Apache/2.2.14 (Win32)",                
        "Content-Length" => strlen($responseBody), 
        "Connection" => "Closed",          
        "Content-Type" => "text/html; charset=utf-8",       
    ];
   
    outputHttpResponse($statusCode, $statusMessage, $headers, $responseBody);
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
    
    return array(
        "method" => trim($methodAndURI[0]),
        "uri" => trim($methodAndURI[1]),
        "headers" => $headers,
        "body" => getStringContentAsPairsArray($body, "&", "="),
    );
}

$http = parseTcpStringAsHttpRequest($contents);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);

/**
 * Gets string as it input and returns a string which let use PHP_EOL as a delimeter
 * 
 * @param $string
 * 
 * @return string
 */
function getStrToParse($string) : string {
    $str = preg_replace("/\r/", "", $string);
    $str = preg_replace("/\n/", PHP_EOL, $str);

    return $str;
}

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

/**
 * @param string $string
 * @param string $arrLastItem
 * 
 * @return bool
 */
function hasBody($string, $arrLastItem) : bool {
    return preg_match('/Content-Length:/', $string) || !preg_match('/:/', $arrLastItem);
}

/**
 * Analyzes request anr return status code for response
 * 
 * @param string    $method
 * @param string    $uri
 * @param string    $contentType
 * @param array     $body
 * 
 * @return int
 */
function getResponseStatusCode($method, $uri, $contentType, $body) : int {
    
    // 400 : Bad Request (invalid method)
    if($method != "POST") return 400;

    // 400 : Bad Request (invalid "Content-type" header)
    if($contentType != "application/x-www-form-urlencoded" ) return 400;

    // 400 : Bad Request (invalid body)
    if(!isRequestBodyValid($body)) return 400;

    // 404 : Not Found (invalid uri)
    if($uri != "/api/checkLoginAndPassword") return 404;
    
    // options: 200, 500, 404
    return getAccessStatus($body);
}

/**
 * Checks if array $body contains "login" and "password" as its keys.
 * 
 * @param array $body
 * 
 * @return bool 
 */
function isRequestBodyValid($body) {
    return (array_key_exists("login", $body) && array_key_exists("password", $body));
}

/**
 * Returns one of 200, 404, 500
 * 
 * @param array $body
 * 
 * @return int
 */
function getAccessStatus($body) : int {

    // 500 : Internal Server Error (file not exist)
    if(!file_exists(FILE_NAME)) return 500;

    $logAndPassPairsAsArr = getFileContentAsPairsArray(FILE_NAME, PHP_EOL, ":");

    foreach($logAndPassPairsAsArr as $login => $password) {
        
        if($body['login'] == $login) {
            if($body['password'] == $password) {
                
                // login found, password matched
                return 200;
            }

            // login found, but password DIDN'T match
            else        
                break;
        }
    }

    // 404 : Not Found (login or/and password not found)
    return 404;
}

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

/**
 * @param string $str
 * @param string $delim1
 * @param string $delim2
 * 
 * @return array
 */
function getStringContentAsPairsArray($str, $delim1, $delim2) : array {
    $arr = cleanArray(explode($delim1, $str));
    $pairsAsArr = [];
    
    foreach($arr as $pair) {
        $p = explode($delim2, $pair);subject: 
        $pairsAsArr += [trim($p[0])=>trim($p[1])];
    }
    
    return $pairsAsArr;
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
* @param int $statusCode
*
* @return string
*/
function getResponseBody($statusCode) : string {
    
    // example: <h1 style="color:green">FOUND</h1>
    $body = "<h1";

    if(isset(RESPONSE_DATA[$statusCode]['color'])) {
        $body .= ' style="color:' . RESPONSE_DATA[$statusCode]['color'] . '"';
    }

    $body .= ">";
    
    if(isset(RESPONSE_DATA[$statusCode]['bodyMessage'])) {
        $body .= RESPONSE_DATA[$statusCode]['bodyMessage'];
    }
    else {
        $body .=  strtolower(RESPONSE_DATA[$statusCode]['statusMessage']);
    }

    $body .= "</h1>";

    return $body;
}