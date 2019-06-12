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
