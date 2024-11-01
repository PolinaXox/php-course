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
    file_put_contents(FILE_NAME, COUNTER_START_VALUE);
}

$counter = file_get_contents(FILE_NAME);
echo $counter;
$counter++;
file_put_contents(FILE_NAME, $counter);
