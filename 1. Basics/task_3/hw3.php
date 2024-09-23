<?php
date_default_timezone_set("Europe/Kyiv");

/**
 * @return string : input request sample as a string 
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
    echo "HTTP/1.1 $statuscode $statusmessage\n";

    foreach($headers as $header => $value) {         
        echo "$header : $value\n";
    }

    echo "\n$body\n"; 
}

/**
 * @param string    $method
 * @param string    $uri
 * @param array     $headers
 * @param string    $body
 */
function processHttpRequest($method, $uri, $headers=null, $body=null) : void {
    $statuscode = getResponseStatusCode($method, $uri);
    $statusmessage = getResponseStatusMessage($statuscode); 
    $body = getResponseBody($statuscode, $uri);
    $headers = array(
        "Date" => date(DATE_RFC1123),                 
        "Server" => "Apache/2.2.14 (Win32)",
        "Content-Length" => strlen($body),
        "Connection" => "Closed",      
        "Content-Type" => "text/html; charset=utf-8",
    );
   
    outputHttpResponse($statuscode, $statusmessage, $headers, $body);
}

/**
* @param string    $string : input request as a string
*
* @return array : the needed request parts as an array
*/
function parseTcpStringAsHttpRequest($string) {
    $strAsArr = explode("\n", $string);
    $methodAndURI = explode(" ", array_shift($strAsArr));
    $headers = [];
    $body = NULL;

    if($strAsArr) {
        $i = 0;

        while($strAsArr[$i]) {
            $ind = strpos($strAsArr[$i], ":");
            $headers[trim(substr($strAsArr[$i], 0, $ind))] = trim(substr($strAsArr[$i], $ind+1));
            $i++;
        }

        $i++;
        
        if($i < count($strAsArr)) {
            $body = trim($strAsArr[$i]);
        }
    }

    return array(
        "method" => trim($methodAndURI[0]),
        "uri" => trim($methodAndURI[1]),
        "headers" => $headers,
        "body" => $body,
    );
}

$http = parseTcpStringAsHttpRequest($contents);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);

/**
 * @param string    $method
 * @param string    $uri
 * 
 * @return int  status code
 */

// condition combination as a table in "condition_combinations of method and uri.xlsx"
function getResponseStatusCode($method, $uri) : int {
    if($method != "GET" || !preg_match('/\?nums=/i', $uri)) {
        return 400;
    }
    
    if(preg_match('#^/sum\?nums=[\d,]+#i', $uri)) {     // integers only??? 
        return 200;
    }

    return 404;
}

/**
 * @param int $statusCode
 * 
 * @return string string message
 */
function getResponseStatusMessage($statusCode) : string {
    return match($statusCode) {
        200 => "OK",
        400 => "Bad Request",
        404 => "Not Found",
        default => "Undefined Status",
    };
}

/**
 * @param int       $statusCode
 * @param string    $uri
 * 
 * @return string   response body
 */
function getResponseBody($statusCode, $uri) : string {

    // not OK
    if($statusCode != 200) {                                
        return strtolower(getResponseStatusMessage($statusCode));
    }

    // OK
    // "/sum?nums=1,2,3" -> "1,2,3" -> [1, 2, 3]
    $ind = strpos($uri, "=");
    $numsArr = explode(",", substr($uri, $ind+1));  
    
    return array_sum($numsArr);
}