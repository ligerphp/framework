<?php
namespace Core\Validators;
use \Exception;

abstract class CustomValidator {
  public $success=true, $msg='', $field, $additionalFieldData=[],$rule;
  protected $_model;

  public function __construct($model,$params){
    $this->_model = $model;

    if(!array_key_exists('field',$params)){
      throw new Exception("You must add a field to the params array.");
    } else {
      if(is_array($params['field'])){
        $this->field = $params['field'][0];
        array_shift($params['field']);
        $this->additionalFieldData = $params['field'];
      } else {
        $this->field = $params['field'];
      }
    }

    if(!property_exists($model, $this->field)){
      throw new Exception("The field must exist in the model");
    }

    if(!array_key_exists('msg',$params)){
      throw new Exception("You must add a msg to the params array.");
    } else {
      $this->msg = $params['msg'];
    }

    if(array_key_exists('rule',$params)){
      $this->rule = $params['rule'];
    }

    try {
      $this->success = $this->runValidation();
    } catch(Exception $e) {
      echo "Validation Exception on " . get_class() . ": " . $e->getMessage() . "<br />";
    }
  }

  abstract public function runValidation();
}
