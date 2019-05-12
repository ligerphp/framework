<?php

namespace Core\Container;

use Core\Foundation\Api\Framework;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;

class Container {

    public function __construct($routes)
    {
        
     $this->containerBuilder =  new ContainerBuilder();
     $this->containerBuilder->register('context',Routing\RequestContext::class);
     $this->containerBuilder->register('matcher',Routing\Matcher\UrlMatcher::class)
                        ->setArguments([$routes,new Reference('context')]);

    $this->containerBuilder->register('request_stack',HttpFoundation\RequestStack::class);
    $this->containerBuilder->register('controller_resolver',HttpKernel\Controller\ControllerResolver::class);
    $this->containerBuilder->register('argument_resolver',HttpKernel\Controller\ArgumentResolver::class);

    $this->containerBuilder->register('listener.router',HttpKernel\EventListener\RouterListener::class)
                    ->setArguments([new Reference('matcher'),new Reference('request_stack')]);

    $this->containerBuilder->register('listener.response',HttpKernel\EventListener\ResponseListener::class)
                    ->setArguments(['UTF-8']);

    $this->containerBuilder->register('listener.exception',HttpKernel\EventListener\ExceptionListener::class)
                    ->setArguments(['App\Exceptions\ErrorController::exception']);
    
    $this->containerBuilder->register('dispatcher',EventDispatcher\EventDispatcher::class)
        ->addMethodCall('addSubscriber',[new Reference('listener.router')])
        ->addMethodCall('addSubscriber',[new Reference('listener.response')])
        ->addMethodCall('addSubscriber',[new Reference('listener.exception')]);

    $this->containerBuilder->register('framework',Framework::class)
        ->setArguments([$routes
        ]);

    }

    public function getContainer(){
        return $this->containerBuilder;
    }
    
}





                    