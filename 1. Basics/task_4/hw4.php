<?php
date_default_timezone_set("Europe/Kyiv");       // real time, також змінено в php.ini

/**
 * Returns the input request example as a string
 * 
 * @return string 
 */
#++
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
#++
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
 * @param string    $body
 */
/*
 function processHttpRequest($method, $uri, $headers=null, $body=null) : void {
    $statuscode = getResponseStatusCode($method, $uri);
    $statusmessage = getResponseStatusMessage($statuscode); 
    $body = getResponseBody($statuscode, $statusmessage, $uri);
    $headers = [
        "Date" => date(DATE_RFC1123),                 
        "Server" => "Apache/2.2.14 (Win32)",
        "Content-Length" => strlen($body),
        "Connection" => "Closed",      
        "Content-Type" => "text/html; charset=utf-8",
    ];
   
    outputHttpResponse($statuscode, $statusmessage, $headers, $body);
}
*/
function processHttpRequest($method, $uri, $headers, $body) : void {
    $statuscode = getStatusCode($method, $uri, $headers["Content-Type"]);
    $statusmessage = getStatusMessage($statuscode);
    $body = getBody($statuscode, $body);

    $headers = [
        "Date" => date(DATE_RFC1123),                       // the most common format      
        "Server" => "Apache/2.2.14 (Win32)",                // just simular constant string, real result: apache_get_version()???
        "Content-Length" => strlen($body), 
        "Connection" => "Closed",                           // just simular constant string
        "Content-Type" => "text/html; charset=utf-8",       // just simular constant string
    ];
   
    outputHttpResponse($statuscode, $statusmessage, $headers, $body);
}

/**
 * Gets input request sample as a string and returns array of the needed request parts
 * 
 * @param string $string
 *
 * @return array
 */
#++
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
/**
 * Gets string as it input and returns a string which let use PHP_EOL as a delimeter
 * 
 * @param $string
 * 
 * @return string
 */
#++
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
#++
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
#++
function hasBody($string, $arrLastItem) : bool {
    return preg_match('/Content-Length:/', $string) || !preg_match('/:/', $arrLastItem);
}

///////////////////////////////////////////////////////////////////////////////////////////
// MY FUNCTIONS
// condition combination as a table in "condition_combinations_4.xlsx"
// (i don't like the realization...)


/**
 * @param string    $method
 * @param string    $uri
 * 
 * @return int  status code
 */
/*
 // condition combinations as a table in "condition_combinations of method and uri.xlsx"
 function getResponseStatusCode($method, $uri) : int {
    if($method != "GET" || !preg_match('/\?nums=/i', $uri)) {
        return 400;
    }
    
    if(preg_match('#^/sum\?nums=[\d,]+#i', $uri)) { 
        return 200;
    }

    return 404;
}
*/
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


/** 
 * @param int $statusCode
 * 
 * @return string
 */
/*
function getResponseStatusMessage($statusCode) : string {
    return match($statusCode) {
        200 => "OK",
        400 => "Bad Request",
        404 => "Not Found",
        default => "Undefined Status",
    };
}
*/
function getStatusMessage($statusCode){
    return match($statusCode) {
        200 => "OK",
        400 => "Bad Request",
        404 => "Not Found",
        500 => "Internal Server Error",
        default => "Undefined Status",
    };
}

/**
 * @param int       $statusCode
 * @param string    $statusMessage
 * @param string    $uri
 * 
 * @return string
 */
/*
function getResponseBody($statusCode, $statusMessage, $uri) : string {

    // not OK
    if($statusCode != 200) {                                
        return strtolower($statusMessage);
    }

    // OK
    // "/sum?nums=1,2,3" -> "1,2,3" -> [1, 2, 3]
    $ind = strpos($uri, "=");
    $numsArr = explode(",", substr($uri, $ind+1));  
    
    return array_sum($numsArr);
}
*/
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

/////////////////////////////////////////////////////////////////////////////////////////