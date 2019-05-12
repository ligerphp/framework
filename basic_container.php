<?php
require __DIR__.'./vendor/autoload.php';

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DemoService {
    public function helloworld(){
        return "Hello World \n";
    }
}

class DependentService {
    public function __construct(DemoService $demoService)
    {
        $this->demo_service = $demoService;
    }

    public function helloworld(){
        return $this->demo_service->helloworld();
    }
}


$containerBuilder = new ContainerBuilder();
$containerBuilder->register('demo.service','DemoService');
$containerBuilder->register('dependent.service','DependentService')->addArgument(new Reference('demo.service'));

$dependentService = $containerBuilder->get('dependent.service');

echo $dependentService->helloWorld();