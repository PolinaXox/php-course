<?php
/**
 * File name, which contains the counter
 */
define("FILE_NAME", "counter.txt");

/**
 * Counter start value
 */
define("COUNTER_START_VALUE", 0);

if(!file_exists(FILE_NAME)) {
    $file = fopen(FILE_NAME, "w") or die("I canot create a file");
    fwrite($file, COUNTER_START_VALUE);
    fclose($file);
}

$counter = file_get_contents(FILE_NAME);
echo $counter;
$counter++;

//file_put_contents() also closes file 
file_put_contents(FILE_NAME, $counter);