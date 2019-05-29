<?php
namespace Core\Foundation\Api;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Core\Foundation\Listeners\ContentListener;
use Core\Foundation\Listeners\StringResponseListener;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Core\Http\Response;
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
       
        try {
        parent::__construct($dispatcher,$controllerResolver,$requestStack,$argumentResolver);
            
        } catch (ResourceNotFoundException $e) {
            echo $e->getMessage();
        } catch (\Exception $exception) {
            $response = new Response('An error occurred', 500);
            return $response;
        }

    
    }


}
