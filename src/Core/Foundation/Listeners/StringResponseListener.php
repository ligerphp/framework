<?php

namespace Core\Foundation\Listeners;


use Core\Http\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
class StringResponseListener implements EventSubscriberInterface{

    public function onView(GetResponseForControllerResultEvent $event){

        $response = $event->getControllerResult();
        if(is_string($response)){
            $event->setResponse(new Response($response));
        }else if(is_array($response)){
            $event->setResponse(new Response(json_encode($response)));
        }else if(is_bool($response)){
            $event->setResponse(new Response($response));
        }else{
            $event->setResponse(new Response($response));
        }
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.view' => 'onView'];
    }
}