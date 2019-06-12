<?php

namespace Core\Foundation\Events;
 
use Symfony\Component\EventDispatcher\Event;
use Core\Http\Request;
use Symfony\Component\HttpFoundation\Response;
// use Core\Http\Response;

class ResponseEvent extends Event {

    private $request,$response;

    public function __construct(Response $response,Request $request)
    {
        $this->response = $response;
        $this->request = $request;
    }
    public function getResponse(){
        return $this->response;
    }

    
    public function getRequest(){
        return $this->request;
    }

    
}