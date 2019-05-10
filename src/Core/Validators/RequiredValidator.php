<?php
namespace Core\Validators;
use Core\Validators\CustomValidator;

class RequiredValidator extends CustomValidator {
  public function runValidation(){
    $value = trim($this->_model->{$this->field});
    $passes = ($value != '' && isset($value));
    return $passes;
  }
}
