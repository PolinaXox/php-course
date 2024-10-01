<?php
define("FILE_NAME", "counter.txt");
define("COUNTER_START_VALUE", 0);

if(!file_exists(FILE_NAME)) {

    // the same as 
    // file_put_contents(FILE_NAME, COUNTER_START_VALUE);
    $file = fopen(FILE_NAME, "w") or die("I canot create a file");
    fwrite($file, COUNTER_START_VALUE);
    fclose($file);
}

$counter = file_get_contents(FILE_NAME);
echo $counter;
$counter++;

//file_put_contents() also closes files 
file_put_contents(FILE_NAME, $counter);