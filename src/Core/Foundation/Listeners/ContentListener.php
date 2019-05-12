<?php

namespace Core\Foundation\Listeners;

use Core\Foundation\Events\ResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentListener implements EventSubscriberInterface{

    public function onResponse(ResponseEvent $event){

        $response = $event->getResponse();
        $headers = $response->headers;
                if(!$headers->has('Content-Lenght') && !$headers->has('Transfer-Encoding')){
                    $headers->set('Content-Lenght',strlen($response->getContent()));
                }

    }

    public static function getSubscribedEvents()
    {
        return ['response'=>['onResponse',-255]];
    }
}