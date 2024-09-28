<?php
define("FILE_NAME","counter.txt");

$counter = file_get_contents(FILE_NAME);
echo $counter;
$counter++;
file_put_contents(FILE_NAME, $counter);