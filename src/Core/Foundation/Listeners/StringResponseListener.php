<?php

namespace Core\Foundation\Listeners;


use Core\Http\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
class StringResponseListener implements EventSubscriberInterface{

    public function onView(GetResponseForControllerResultEvent $event){
        $response = $event->getControllerResult();
        
        if(is_string($response) || is_array($response)){
            $event->setResponse(new Response($response));
        }
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.view' => 'onView'];
    }
}