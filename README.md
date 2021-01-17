# mClient

PHP Curl Class. Require php >= 7.4

Installation
```
composer require mehmetbeyhz/mclient:dev-master
```

## GET
```php
<?php 
try{
    $r = Request::get('https://api.github.com/users/mehmetbeyhz')
        ->addCurlOptions(CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36')
        ->execute()
        ->getResponse();
    print_r($r);
}catch (Exception $e)
{
    echo $e->getMessage();
}
```

Request Details
```php
<?php 
try{
    $r = Request::get('https://api.github.com/users/mehmetbeyhz')->execute();
    print_r($r->getResponse()); // Direct Response
    print_r($r->getHeaderLine('http_code')); // response headers key
    print_r($r->getResponseHeaders()); // response headers as array
    print_r($r->getCookies()); // key1=value1; key2=value2; 
    print_r($r->getCookies('key1')); // value1
}catch (Exception $e)
{
    echo $e->getMessage();
}
```


## POST
```php
<?php 
try{
    $r = (new Request('https://api.example.com/login'))
        ->addPost('username','mt.ks')
        ->addPost('password','123456')
        ->addCurlOptions(CURLOPT_RETURNTRANSFER,false)
        // ->setProxy('proxyUser:proxyPass@127.0.0.1:8080')
        // ->setJsonPost(true) :: format => {"username":"mt.ks","password":"123456"}
        ->execute()
        // ->getDecodedResponse(true)  => true : as Array, false : as Object
        ->getResponse(); // direct page response
    print_r($r);
}catch (Exception $e)
{
    echo $e->getMessage();
}
```

## MULTI CURL
```php
<?php 
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
    $v->getResponseText();
    $v->getDecodedResponse(false);
    $v->getIdentifierParams();
    $v->getHeader('http_status');
    $v->getHeaders();
    $v->getCookies()->getCookie();
    $v->getCookies()->getCookie('sessionID');
    $v->getRequest()->getRequestUri();
}
```