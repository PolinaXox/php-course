<?php
// не звертайте на цю функцію уваги
// вона потрібна для того щоб правильно зчитати вхідні дані

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

// parsing 
function parseTcpStringAsHttpRequest($string) {
    $strAsArr = explode("\n", $string);
    $methodAndURI = explode(" ", array_shift($strAsArr));
    
    $headers = array();
    $body = NULL;
    if($strAsArr){
        $i = 0;
        while($strAsArr[$i]){
            $ind = strpos($strAsArr[$i], ":");
			// асоціативний масив, як у завданні
            $headers[trim(substr($strAsArr[$i], 0, $ind))] = trim(substr($strAsArr[$i], $ind+1));
            // масив масивів, як у тестирі домашок
			//array_push($headers, [trim(substr($strAsArr[$i], 0, $ind)), trim(substr($strAsArr[$i], $ind+1))]);
            $i++;
        }
        $i++;                               // empyty line between header(s) and body, if it is
        if($i < count($strAsArr)){          // if smth exist, so it's the body... (or an_empty_line)
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