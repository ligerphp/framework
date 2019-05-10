<?php
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;

if (! function_exists('abort')) {

function abort($code,$message,array $headers=[]){
    if ($code instanceof Response) {
        throw new HttpResponseException($code);
    } elseif ($code instanceof Responsable) {
        throw new HttpResponseException($code->toResponse(request()));
      }
    }
}

if(!function_exists('dd')){
    function dd($value){
        return dump($value);
    }
}

if(!function_exists('auth')){
    function auth(){
        return new AuthenticationProviderManager([]);
    }
}