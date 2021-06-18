<?php

use MClient\Request;

require "../vendor/autoload.php";

// Get github user info


try {
    $req =  Request::get('https://jsonplaceholder.typicode.com/todos/1')
        ->setProxy('login1:pass1@188.59.41.156:8186')
        ->execute();

    print_r($req->getResponseHeaders());

} catch (Exception $e) {
    echo $e->getMessage();
}
