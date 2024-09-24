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

// process the response
function processHttpRequest($method, $uri, $headers, $body) {
    $statuscode = getStatusCode($method, $uri, $headers["Content-Type"]);
    $statusmessage = getStatusMessage($statuscode); // залежить від статусКод
    $body = getBody($statuscode, $body);

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
// condition combination as a table in "condition_combinations_4.xlsx"
// (i don't like the realization...)
function getStatusCode($method, $uri, $contentType) : int {
    // invalid method or Content-Type
    if($method != "POST" && $contentType != "application/x-www-form-urlencoded" ) {
        return 400;
    }
    // invalid uri
    if($uri != "/api/checkLoginAndPassword") {
        return 404;
    }
    // file is NOT exist
   if(!file_exists("passwords.txt")) {
        return 500;
    }

    return 200;
}
// request code -> request string
function getStatusMessage($statusCode){
    return match($statusCode) {
        200 => "OK",
        400 => "Bad Request",
        404 => "Not Found",
        500 => "Internal Server Error",
        default => "Undefined Status",
    };
}

// form body: error message or message for user
function getBody($statusCode, $requestBody) : string {
    if($statusCode != 200) {                                // not OK (== server work is not OK)
        return strtolower(getStatusMessage($statusCode));
    }

    // OK (== server work is OK)
    //login=student&password=12345
    $bodyAsArray = explode("&", $requestBody);
    $logPassArr = array();
    $i = 0;
    while($i < count($bodyAsArray)){
        $keyValuePair = explode("=", $bodyAsArray[$i]);
        $logPassArr[$keyValuePair[0]] = $keyValuePair[1];
        $i++;
    }
    // invalid requestBody
    if(!(array_key_exists("login", $logPassArr) && array_key_exists("password", $logPassArr))) {
        return
            '<h1 style="color:magenta">SOMETHING WRONG...<br>Try again later or contact to your admin</h1>';
    }

    // pair "log/pass" exist: log - OK, pass - OK -> Welcome!

    // pair "log/pass" DOES NOT exist -> Зареєструватися?
    
    // pair "log/pass" exist: log - OK, pass - NOT OK -> Забули пароль? 
    
    return "OK";
}