<?php

use MClient\MultiRequest;
use MClient\Request;

require "../vendor/autoload.php";

$multiCurl = new MultiRequest();

$multiCurl->add(Request::get("https://jsonplaceholder.typicode.com/todos/1")
    ->setUserAgent("my useragent")
);

$multiCurl->add(Request::get("https://jsonplaceholder.typicode.com/todos/2")
    ->setUserAgent("my useragent")
);

$multiCurl->add(Request::get("https://jsonplaceholder.typicode.com/todos/3")
    ->setUserAgent("my useragent")
    ->setIdentifierParams(['client' => 1])
);

$multiCurl->add(Request::get("https://jsonplaceholder.typicode.com/todos/4")
    ->setUserAgent("my useragent")
    ->setIdentifierParams(['clientId' => 80])
);


foreach ($multiCurl->execute() as $v) {
    print_r($v->getResponseText());
}