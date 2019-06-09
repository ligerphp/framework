<?php

use Core\Container\Container;

if (! function_exists('abort')) {

function abort($code,$message,array $headers=[]){
    if ($code instanceof Response) {
        throw new HttpResponseException($code);
    } elseif ($code instanceof Responsable) {
        throw new HttpResponseException($code->toResponse(request()));
      }
    }
}

if(!function_exists('dd')){
    function dd($value){
        return dump($value);
    }
}
/**
 * Return the container 
 */
if(!function_exists('app')){
    function app($service){
        $container = Container::getInstance();
       return $container->getContainer()->get($service);
    }
}

if(!function_exists('auth')){
    function auth(){
        return app('auth');
    }
}



if(!function_exists('response')){
    /**
 * Return the liger response service provider
 */
    function response(){
        return app('response');
    }
}

if (! function_exists('env_')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env_($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof \Closure ? $value() : $value;
    }
}
if (! function_exists('storage_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param  string  $path
     * @return string
     */
    function storage_path($path = '')
    {
        return app('path.storage').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}
if (! function_exists('session')) {
    /**
     * Get the session service provider.
     *
     * @param  string  $path
     * @return string
     */
    function session()
    {
        return app('session');
    }
}

if(!function_exists('auth')){
    /**
     * Auth service provider
     * 
     */
    function auth(){
        return app('auth');
    }
}

if(!function_exists('form')){
    /**
     * Form helpers
     */
    function form(){
        return app('form');
    }
}

if(!function_exists('sanitize')){

    /**
     * Santize a given input
     * 
     * @param $data required mixed
     * 
     * @return mixed
     */
    function sanitize($data){
        htmlentities($data);
        trim($data);
        htmlspecialchars($data);
        return $data;
    }
}