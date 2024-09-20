
<?php
date_default_timezone_set("Europe/Kyiv");

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

//$contents = readHttpLikeInput();

$contents = "GET /sum?nums=1,2,3 HTTP/1.1\nHost: student.shpp.me\n\n";

// for age-cases testing
//$contents = "GET /doc/test.html HTTP/1.1\n" 
//."Host: www.test101.com\nAccept: image/gif, image/jpeg, */*\nAccept-Language: en-us\n"
//."Accept-Encoding: gzip, deflate\nUser-Agent: Mozilla/4.0\nContent-Length: 35\n"
//."\nbookId=12345&author=Tan+Ah+Teck";

function outputHttpResponse($statuscode, $statusmessage, $headers, $body) {
    
    echo "HTTP/1.1 $statuscode $statusmessage\n"; 
    foreach($headers as $header => $value){
        echo "$header : $value\n";
    }
    echo "\n$body\n";
   
}

function processHttpRequest($method, $uri, $headers=null, $body=null) {
    // first HTTP/1.1 
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

function parseTcpStringAsHttpRequest($string) {
    $strAsArr = explode("\n", $string);
    $methodAndURI = explode(" ", array_shift($strAsArr));
    if($strAsArr){
        $headers = array();
        $i = 0;
        while($strAsArr[$i]){
            $ind = strpos($strAsArr[$i], ":");
            // assiciative array
            $headers[trim(substr($strAsArr[$i], 0, $ind))] = trim(substr($strAsArr[$i], $ind+1));
            // matrix
            #array_push($headers, [trim(substr($strAsArr[$i], 0, $ind)), trim(substr($strAsArr[$i], $ind+1))]);
            $i++;
        }
        $i++;
        $body = trim($strAsArr[$i]);
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
function getStatusCode($method, $uri) : int {
    if($method != "GET" || !preg_match('/\?nums=/i', $uri)) {
        return 400;
    }
    
    if(preg_match('#^/sum\?nums=[\d,]+#i', $uri)) { 
        return 200;
    }

    return 404;
}

function getStatusMessage($statusCode){
    return match($statusCode) {
        200 => "OK",
        400 => "Bad Request",
        404 => "Not Found",
        default => "Undefined Status",
    };
}

function getBody($statusCode, $uri) : string {
    if($statusCode != 200) {
        return strtolower(getStatusMessage($statusCode));
    }
    
    $ind = strpos($uri, "=");
    $numsArr = explode(",", substr($uri, $ind+1));
    return array_sum($numsArr);
}
