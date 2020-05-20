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
    $r = (new Request('https://api.github.com/users/mehmetbeyhz'))
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
    $r = (new Request('https://api.github.com/users/mehmetbeyhz'))->execute();
    print_r($r->getResponse()); // Direct Response
    print_r($r->getHeaderLine('http_code')); // response headers key
    print_r($r->getResponseHeaders()); // response headers as array
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