<?php
// Запитання:
// HTTP/1.1 ? не рахується?
// чи потрібно передавати порожні рядки, якщо якась частина URI відсутня? 


// не звертайте на цю функцію уваги
// вона потрібна для того щоб правильно зчитати вхідні дані
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

// for age-cases testing
//$contents = "GET /doc/test.html HTTP/1.1\n" 
//."Host: www.test101.com\nAccept: image/gif, image/jpeg, */*\nAccept-Language: en-us\n"
//."Accept-Encoding: gzip, deflate\nUser-Agent: Mozilla/4.0\nContent-Length: 35\n";
//."\nbookId=12345&author=Tan+Ah+Teck";


function parseTcpStringAsHttpRequest($string) {
    $strAsArr = explode("\n", $string);
    $methodAndURI = explode(" ", array_shift($strAsArr));
    if($strAsArr){
        $headers = array();
        $i = 0;
        while($strAsArr[$i]){
            $ind = strpos($strAsArr[$i], ":");
			// асоціативний масив, як у завданні
            #$headers[trim(substr($strAsArr[$i], 0, $ind))] = trim(substr($strAsArr[$i], $ind+1));
            // масив масивів, як у тестирі домашок
			array_push($headers, [trim(substr($strAsArr[$i], 0, $ind)), trim(substr($strAsArr[$i], $ind+1))]);
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
echo(json_encode($http, JSON_PRETTY_PRINT));