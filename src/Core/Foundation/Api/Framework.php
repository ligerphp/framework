<?php
namespace Core\Foundation\Api;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\Routing\RequestContext;
use Core\Foundation\Listeners\ContentListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Core\Foundation\Listeners\StringResponseListener;
use Core\Http\Request;
class Framework extends HttpKernel implements HttpKernelInterface{
    
    protected $matcher, $controllerResolver, $argumentResolver, $dispatcher;


    public function __construct($route)
    {

            $requestStack = app('request_stack');
            $controllerResolver = app('controller_resolver');
            $argumentResolver = app('argument_resolver');
            $dispatcher = app('dispatcher');
            $dispatcher->addSubscriber(new ContentListener());
            $dispatcher->addSubscriber(new StringResponseListener());
            
        parent::__construct($dispatcher,$controllerResolver,$requestStack,$argumentResolver);

    
    }


}
