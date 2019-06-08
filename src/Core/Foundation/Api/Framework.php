<?php
namespace Core\Foundation\Api;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Core\Foundation\Listeners\ResponseListener;
use Core\Foundation\Events\ResponseEvent;

class Framework extends HttpKernel implements HttpKernelInterface{
    
    protected $requestStack, $controllerResolver, $argumentResolver, $dispatcher;


    public function __construct($route)
    {      $req =  app('request')->createFromGlobals();
            $res = app('response');
            $this->requestStack = app('request_stack');
            $this->controllerResolver = app('controller_resolver');
            $this->argumentResolver = app('argument_resolver');
            $this->dispatcher = app('dispatcher');    
            $this->dispatcher->addSubscriber(new ResponseListener());
            $this->dispatcher->dispatch('corsResponse',new ResponseEvent($res,$req));

            parent::__construct($this->dispatcher,$this->controllerResolver,$this->requestStack,$this->argumentResolver);
        
    }


}
