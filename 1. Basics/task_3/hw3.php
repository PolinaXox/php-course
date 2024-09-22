<?php
date_default_timezone_set("Europe/Kyiv");       // real time, також змінено в php.ini

// read from console
function readHttpLikeInput() {
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

// output the response
function outputHttpResponse($statuscode, $statusmessage, $headers, $body) {
    echo "HTTP/1.1 $statuscode $statusmessage\n"; 
    foreach($headers as $header => $value){         // for associative arr, not for matrix
        echo "$header : $value\n";
    }
    echo "\n$body\n"; 
}

// make the response
function processHttpRequest($method, $uri, $headers=null, $body=null) {
    $statuscode = getStatusCode($method, $uri);
    $statusmessage = getStatusMessage($statuscode); // залежить від статусКод
    $body = getBody($statuscode, $uri);

    $headers = array(
        "Date" => date(DATE_RFC1123),                       // the most common format      
        "Server" => "Apache/2.2.14 (Win32)",                // just simular constant string, real result: apache_get_version()???
        "Content-Length" => strlen($body), 
        "Connection" => "Closed",                           // just simular constant string
        "Content-Type" => "text/html; charset=utf-8",       // just simular constant string
    );
   
    outputHttpResponse($statuscode, $statusmessage, $headers, $body);
}

// parsing the request
function parseTcpStringAsHttpRequest($string) {
    $strAsArr = explode("\n", $string);
    $methodAndURI = explode(" ", array_shift($strAsArr));
    $headers = array();
    $body = NULL;
    if($strAsArr){
        $headers = array();
        $i = 0;
        while($strAsArr[$i]){
            $ind = strpos($strAsArr[$i], ":");
            $headers[trim(substr($strAsArr[$i], 0, $ind))] = trim(substr($strAsArr[$i], $ind+1));
            $i++;
        }
        $i++;
        if($i < count($strAsArr)){
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

///////////////////////////////////////////////////////////////////////////////////////////
// MY FUNCTIONS
// condition combination as a table in "condition_combinations of method and uri.xlsx"
// (i don't like the realization...)
function getStatusCode($method, $uri) : int {
    if($method != "GET" || !preg_match('/\?nums=/i', $uri)) {
        return 400;
    }
    
    if(preg_match('#^/sum\?nums=[\d,]+#i', $uri)) {     // integers only??? 
        return 200;
    }

    return 404;
}

// request code -> request string
function getStatusMessage($statusCode){
    return match($statusCode) {
        200 => "OK",
        400 => "Bad Request",
        404 => "Not Found",
        default => "Undefined Status",
    };
}

// form body: string or sum
function getBody($statusCode, $uri) : string {
    if($statusCode != 200) {                                // not OK
        return strtolower(getStatusMessage($statusCode));
    }
    // OK
    $ind = strpos($uri, "=");
    $numsArr = explode(",", substr($uri, $ind+1));  // "/sum?nums=1,2,3" -> "1,2,3" -> [1, 2, 3]
    return array_sum($numsArr);
}