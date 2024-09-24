<?php

/**
 * Returns the input request example as a string
 * 
 * @return string 
 */
function readHttpLikeInput() {
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
    
    return array(
        "method" => trim($methodAndURI[0]),
        "uri" => trim($methodAndURI[1]),
        "headers" => $headers,
        "body" => $body,
    );
}

$http = parseTcpStringAsHttpRequest($contents);
echo(json_encode($http, JSON_PRETTY_PRINT));

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