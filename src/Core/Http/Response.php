<?php
namespace Core\Http;

use Symfony\Component\HttpFoundation\Response as SympResponse;

class Response extends SympResponse{


    public static function json(array $arry){
        if(is_array($arry)){
             $encoded = json_encode($arry);
            return new parent($encoded,200,['Content-Type'=> 'application/json']);
        }
    }
    
}


// $response = new Response();

// $response->setContent('Hello world!');
// $response->setStatusCode(200);
// $response->headers->set('Content-Type', 'text/html');

// // configure the HTTP cache headers
// $response->setMaxAge(10);
