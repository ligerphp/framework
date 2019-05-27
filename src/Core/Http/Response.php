<?php
namespace Core\Http;

use Symfony\Component\HttpFoundation\Response as SympResponse;

class Response extends SympResponse{



    public static function json(array $arry,$code =200){
        if(is_array($arry)){
             $encoded = json_encode($arry);
            return new parent($encoded,$code,['Content-Type'=> 'application/json']);
        }
    }

    public  function withRedirect($location = '/'){
        if (!headers_sent()) {
            header('Location: ' . $location);
            exit();
            return static::self;
        }
    }
    
}


// $response = new Response();

// $response->setContent('Hello world!');
// $response->setStatusCode(200);
// $response->headers->set('Content-Type', 'text/html');

// // configure the HTTP cache headers
// $response->setMaxAge(10);
