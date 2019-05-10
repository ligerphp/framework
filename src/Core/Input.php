<?php
namespace Core;
use Core\FH;
use Core\Router;

class Input {

  public function isPost(){
    return $this->getRequestMethod() === 'POST';
  }

  public function isPut(){
    return $this->getRequestMethod() === 'PUT';
  }

  public function isGet(){
    return $this->getRequestMethod() === 'GET';
  }

  public function getRequestMethod(){
    return strtoupper($_SERVER['REQUEST_METHOD']);
  }

  public function get($input=false) {
    if(!$input){
      // return entire request array and sanitize it
      $data = [];
      foreach($_REQUEST as $field => $value){
        $data[$field] = trim(FH::sanitize($value));
      }
      return $data;
    }

    return (array_key_exists($input,$_REQUEST))?trim(FH::sanitize($_REQUEST[$input])) : '';
  }

  public function csrfCheck(){
    if(!FH::checkToken($this->get('csrf_token'))) Router::redirect('restricted/badToken');
    return true;
  }
}
