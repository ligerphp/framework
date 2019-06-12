<?php
namespace Core\Validators;
use Core\Validators\CustomValidator;
use Core\Validators\Exceptions\InvalidEmailFormateException;
class EmailValidator extends CustomValidator {

  public function runValidation(){
    
    if(!empty($this->dataToValidate)){
      $pass = filter_var($this->dataToValidate, FILTER_VALIDATE_EMAIL);
    }
    return $pass;

  }

  public function throw($message){
    throw new InvalidEmailFormateException($message);
  }
  
}
