<?php
namespace Core\Foundation\Api;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Core\Http\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symphony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Request;

class Framework implements HttpKernelInterface {
    
    protected $matcher;
    protected $controllerResolver;
    protected $argumentResolver;

    public function __construct(UrlMatcher $matcher, ControllerResolver $controllerResolver, ArgumentResolver $argumentResolver)
    {
        $this->matcher = $matcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
    }


public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST,
$catch = true){
try {

    $matched = $this->matcher->match($request->getPathInfo());

    //add attributes to request object
    $request->attributes->add($matched);
    
    $controller = $this->controllerResolver->getController($request);    
    $arguments = $this->argumentResolver->getArguments($request,$controller);

    return call_user_func_array($controller, $arguments);

} catch (ResourceNotFoundException $exception) {
    return new Response('Not Found '.$exception, 404);
} catch (Exception $exception) {
    return new Response('An error occurred', 500);
}
    }
}
