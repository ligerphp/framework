<?php
namespace Core\Validators;
use \Exception;

abstract class CustomValidator {
  public $success=true, $msg='', $field, $additionalFieldData=[],$rule;
  protected $_model;

  public function __construct($params){
    $this->dataToValidate = $params;

    
  }

  abstract public function runValidation();
}
