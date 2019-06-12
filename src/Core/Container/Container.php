<?php

namespace Core\Container;

use Core\Foundation\Api\Framework;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;
use Core\Http\Request;
use Core\Auth\AuthServiceProvider;
use Core\Http\Response;
use Core\Foundation\Listeners\ResponseListener;
use Core\Foundation\Listeners\ContentListener;
use Core\Foundation\Listeners\StringResponseListener;

class Container {
    public static $_routes =  null;
    private static $_instance = null;
    private $containerBuilder;

    public function __construct($routes)
    {
        self::$_routes = $routes;

     $this->containerBuilder =  new ContainerBuilder();
     $this->containerBuilder->register('context',Routing\RequestContext::class)
                            ->addMethodCall('fromRequest',[Request::createFromGlobals()]);    
     $this->containerBuilder->register('matcher',Routing\Matcher\UrlMatcher::class)
                        ->setArguments([self::$_routes,new Reference('context')]);
    
    $this->containerBuilder->register('request_stack',HttpFoundation\RequestStack::class);
    $this->containerBuilder->register('controller_resolver',HttpKernel\Controller\ControllerResolver::class);
    $this->containerBuilder->register('argument_resolver',HttpKernel\Controller\ArgumentResolver::class);

    $this->containerBuilder->register('listener.router',HttpKernel\EventListener\RouterListener::class)
                    ->setArguments([new Reference('matcher'),new Reference('request_stack')]);

    $this->containerBuilder->register('request',Request::class);
    $this->containerBuilder->register('response',Response::class);

    $this->containerBuilder->register('auth',AuthServiceProvider::class)
                           ->setArguments([new Reference('request')]);

    $this->containerBuilder->register('listener.response',HttpKernel\EventListener\ResponseListener::class)
                    ->setArguments(['UTF-8']);
        
    $this->containerBuilder->register('listener.cors_response',ResponseListener::class);

    $this->containerBuilder->register('session',\Core\Session\Session::class);
    
    $this->containerBuilder->register('listener.exception',HttpKernel\EventListener\ExceptionListener::class)
                    ->setArguments(['App\Exceptions\ErrorController::exception']);
    
    $this->containerBuilder->register('dispatcher',EventDispatcher\EventDispatcher::class)
        ->addMethodCall('addSubscriber',[new Reference('listener.router')])
        ->addMethodCall('addSubscriber',[new Reference('listener.response')])
        ->addMethodCall('addSubscriber',[new Reference('listener.exception')])
        ->addMethodCall('addSubscriber',[new Reference('listener.cors_response')])
        ->addMethodCall('addSubscriber',[new ContentListener()])
        ->addMethodCall('addSubscriber',[new StringResponseListener()]);

    $this->containerBuilder->register('framework',Framework::class)
        ->setArguments([self::$_routes
        ]);

    }

    public function getContainer(){
        return $this->containerBuilder;
    }

    public static function getInstance(){

        if(!isset(self::$_instance)) {
            self::$_instance = new self(self::$_routes);
          }
          return self::$_instance;
    }

    /**
     * 
     * Bind service to the container
     */
    public function instantiate($alias,$abstract){
        self::getInstance()->getContainer()->register($alias,$abstract);
    }
    
}





                    