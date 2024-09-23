<?php
/**
 * @return string the input request example as a string
 */
function readHttpLikeInput() : string {
    $f = fopen( 'php://stdin', 'r' );
    $store = "";
    $toread = 0;
    while( $line = fgets( $f ) ) {
        $store .= preg_replace("/\r/", "", $line);

        // store a request body length if the body exsist
        // $m - array of matches
        // $m[1] <- (\d+); $m[1]*1 - (str->int) - the length of the body
        if (preg_match('/Content-Length: (\d+)/',$line,$m)) {
            $toread=$m[1]*1;
        }

        if ($line == "\r\n") 
              break;
    }

    // if the request has a body, read and store it
    if ($toread > 0) 
        $store .= fread($f, $toread);
    
    return $store;
}

$contents = readHttpLikeInput();

/**
 * @param string $string : input request sample as a string
 *
 * @return array of the needed request parts
 */
function parseTcpStringAsHttpRequest($string) : array {
    $strAsArr = explode("\n", $string);
    $methodAndURI = explode(" ", array_shift($strAsArr));
    $headers = [];
    $body = NULL;

    // if the request has headers store each header data as
    // $headers[headerName] = headerValue
    if($strAsArr) {
        $i = 0;
        while($strAsArr[$i]) {
            $ind = strpos($strAsArr[$i], ":");
            $headers[trim(substr($strAsArr[$i], 0, $ind))] = trim(substr($strAsArr[$i], $ind+1));
            $i++;
        }

        // empyty line between header(s) and body, if it is
        $i++;
        
        // if the request has a body
        if($headers["Content-Length"]) {          
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
echo(json_encode($http, JSON_PRETTY_PRINT));