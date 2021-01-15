<?php

use MClient\MultiRequest;
use MClient\Request;

require "../vendor/autoload.php";

$multiCurl = new MultiRequest();


$multiCurl->add((new Request("https://jsonplaceholder.typicode.com/todos/1"))
    ->setUserAgent("my useragent")
);

$multiCurl->add((new Request("https://jsonplaceholder.typicode.com/todos/2"))
    ->setUserAgent("my useragent")
);

$multiCurl->add((new Request("https://jsonplaceholder.typicode.com/todos/3"))
    ->setUserAgent("my useragent")
    ->setIdentifierParams("")
    ->setIdentifierParams(['clientId' => 5])
);


$multiCurl->add((new Request("https://jsonplaceholder.typicode.com/todos/4"))
    ->setUserAgent("my useragent")
    ->setIdentifierParams(['clientId' => 80])
);


foreach ($multiCurl->execute() as $v) {
    $v->getResponseText();
    $v->getDecodedResponse(false);
    $v->getIdentifierParams();
    $v->getHeader('http_status');
    $v->getHeaders();
    $v->getCookies()->getCookie();
    $v->getCookies()->getCookie('sessionID');
    $v->getRequest()->getRequestUri();
}