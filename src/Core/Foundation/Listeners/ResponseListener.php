<?php

namespace Core\Foundation\Listeners;

use Core\Foundation\Events\ResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResponseListener implements EventSubscriberInterface{

    public function onCorsResponse(ResponseEvent $event){

            if(file_exists(ROOT . DS . 'config' . DS . 'cors.php')){
                $corsConfig =  include ROOT .DS . 'config' . DS . 'cors.php';
                foreach ($corsConfig as $key => $value) {
                    if($key == 'allowedOrigins'){
                        //set allowed orign headers
                        if(count($value) == 1 && $value[0] == '*'){
                        header('Access-Control-Allow-Origin:*');
                        }
                    }
                    // $event->getResponse()->headers->set($key,$value);
                    
                }
            }; 
            header('Access-Control-Allow-Methods:POST,GET,PUT,OPTIONS,PATCH,DELETE');
            header('Access-Control-Allow-Credentials:true');
            header('Access-Control-Allow-Headers:Authorization,Content-Type,x-xsrf-token,x_csrftoken,Cache-Control,X-Requested-With');

    }

    public static function getSubscribedEvents()
    {
        return ['corsResponse' => 'onCorsResponse'];
    }
}