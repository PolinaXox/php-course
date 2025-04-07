<?php
require __DIR__ . '/../../vendor/autoload.php';
require 'cookie_sets.php';

try {
    RequestValidatorService::validate('DELETE');

    // on server
    if (!isset($_COOKIE['HTTPSESSION'])) {
        throw new Exception('Session is not active', 401);
    }

    session_id($_COOKIE['HTTPSESSION']);
    session_start();

    $index = new UserToDoTaskDataValidatorService()->getValidToDoTaskId();
    //echo $index;
    new ToDoTaskDAO()->delete($index, $_SESSION['userFile']);

    // to front
    //header('Content-Type: application/json', false);
    echo json_encode(['ok' => true]);

//    $arr = [
//        ['id'=>1, 'text' => 'aaa', 'checked' => true],
//        ['id'=>5, 'text' => 'bbb', 'checked' => true],
//        ['id'=>15, 'text' => 'ccc', 'checked' => false]
//    ];
//    foreach ($arr as $line) {
//        foreach ($line as $key => $value) {
//            echo $key . ' => ' . $value . "\n";
//        }
//        echo PHP_EOL;
//    }
//
//
//    $json = json_encode($arr);
//    var_dump($json);
//    $arr = json_decode($json, true);
//    foreach ($arr as $line) {
//        foreach ($line as $key => $value) {
//            echo $key . ' => ' . $value . "\n";
//        }
//        echo PHP_EOL;
//    }

} catch (Exception $ex) {
    http_response_code($ex->getCode());
    echo json_encode(['exMessage' => $ex->getMessage()]);
    exit();
}
