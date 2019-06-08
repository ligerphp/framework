<?php

namespace Core\Foundation\Listeners;

use Core\Foundation\Events\ResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentListener implements EventSubscriberInterface{

    public function onResponse(ResponseEvent $event){

        $response = $event->getResponse();
        $headers = $response->headers;
                if(!$headers->has('Content-Length') && !$headers->has('Transfer-Encoding')){
                    $headers->set('Content-Length',strlen($response->getContent()));
                }
                if($headers->has('Content-Type')){
                    $headers->set('Content-Type','Application/json');
                }
    }

    public static function getSubscribedEvents()
    {
        return ['response'=>['onResponse']];
    }
}