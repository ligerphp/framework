<?php

namespace Core\Container;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class Container {

   protected $instances = [];
    /**
     * containers
     */
    protected $container = [],$containerBuilder;


    public function __construct(){
     $this->containerBuilder =  new ContainerBuilder();
    }

    public function register($id,$service,$refrence = false){
        
        if(!$refrence):
            $this->containerBuilder->register($id,$service);
            array_push($this->instances,['instance'=>$id,'service' => $service]);
        else:
            $this->regsterWithArguments($id,$service,$refrence);
        endif;

    }

    public function regsterWithArguments($id,$serivice,$refrence){
        $this->containerBuilder->register($id,$serivice)->addArgument(new Reference($refrence));

    }
    /**
     * Check if a service is registered in the container
     */
    public function check($a){
      return $this->instaces[$a];
    }

}