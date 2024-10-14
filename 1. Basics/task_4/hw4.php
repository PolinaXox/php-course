<?php
/**
 * File to search logins and passwords
 */
define(constant_name: "FILE_NAME", value: "passwords.txt");

/**
 * Array contains data for response forming according its status code
 */
define("RESPONSE_DATA", [
    "200" => ["statusMessage" => "OK", "color" => "green"],
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
    $response = getResponse($method, $uri, $headers, $body);
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
    
    return array(
        "method" => trim($methodAndURI[0]),
        "uri" => trim($methodAndURI[1]),
        "headers" => $headers,
        "body" => getStringContentAsPairsArray($body, "&", "="),
    );
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

// FILES PROCESSING FUNCTIONS ////////////////////////////////////////////////////

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

// STRINGS PROCESSING FUNCTIONS ////////////////////////////////////////////////////

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

// RESPONSE MAKING FUNCTIONS///////////////////////////////////////////////////////////

/**
 * Forms the server response depends on the client request and server capabilities(file existance)
 * 
 * @param string    $requestMethod
 * @param string    $requestUri
 * @param array     $requestHeaders
 * @param array     $requestBody
 * @return array
 */
function getResponse($requestMethod, $requestUri, $requestHeaders, $requestBody) : array {
    
    try{
        checkRequestMethod($requestMethod);
        checkRequestUri($requestUri);
        checkRequestContentTypeValue($requestHeaders["Content-Type"]);
        checkRequestBody($requestBody);
        checkFileExistance(FILE_NAME);

        $codeAndMessage = getAccess($requestBody);
        $statusCode = $codeAndMessage['statusCode'];
        $bodyMessage = $codeAndMessage['bodyMessage'];
    }
    catch(Exception $ex) {
        $statusCode = $ex->getCode();
        $bodyMessage = $ex->getMessage();
    };

    $statusMessage = getResponseStatusMessage($statusCode);
    $body = getResponseBody($statusCode, $bodyMessage);
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
 * @throws Exception
 * @return void
 */
function checkRequestMethod(string $method) : void {
    if( $method === "POST") return;

    throw new Exception("Method is invalid. The method POST is needed.", 400);
}

/**
 * @param string $uri
 * @throws Exception
 * @return void
 */
function checkRequestUri(string $uri) : void {
    if($uri === "/api/checkLoginAndPassword") return;

    throw new Exception("URI is invalid.", 400);
}

/**
 * @param string $contentType
 * @throws Exception
 * @return void
 */
function checkRequestContentTypeValue(string $contentType) : void {
    if($contentType === "application/x-www-form-urlencoded") return;
    
    throw new Exception("Header's 'Content-Type' value is invalid.", 400);
}

/**
 * @param array $body
 * @throws Exception
 * @return void
 */
function checkRequestBody(array $body) : void {
    if(array_key_exists("login", $body) && array_key_exists("password", $body)) return;
    
    throw new Exception("Request body doesn't content 'login' or/and 'password' variable(s)", 400);
}

/**
 * @param string $file
 * @throws Exception
 * @return void
 */
function checkFileExistance(string $file) : void {
    if(file_exists($file)) return;
    
    throw new Exception('File ' . $file . ' doesn`t exist', 500);
}

/**
 * Grants or denies access. Returns the result code and message.
 * @param array $body
 * @throws Exception
 * @return array
 */
function getAccess($body) : array {
    $logAndPassPairsAsArr = getFileContentAsPairsArray(FILE_NAME, PHP_EOL, ":");

    foreach($logAndPassPairsAsArr as $login => $password) {
        if($body['login'] == $login) {
            if($body['password'] == $password) {

                // login found, password matched
                return ['statusCode' => 200, 'bodyMessage' => 'FOUND'];
            }

            // login found, but password DIDN'T match
            else
                throw new Exception("Password didn`t match. Fogot password?", 404);        
        }
    }

    // 404 : Not Found (login not found)
    return ['statusCode' => 404, 'bodyMessage' => 'User not found. Would you like to register?'];;
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
 * 
 * @return string
 */
function getResponseBody(int $statusCode, string $bodyMessage) : string {
    
    // example: <h1 style="color:green">FOUND</h1>
    $style = isset(RESPONSE_DATA[$statusCode]['color']) ?
                    ' style="color:' . RESPONSE_DATA[$statusCode]['color'] . '"' :
                    '';
    $body = '<h1'. $style . '>' . $bodyMessage . '</h1>';

    return $body;
}
