<?php
namespace Core\Validators;
use Core\Validators\CustomValidator;

class MatchesValidator extends CustomValidator {

  public function runValidation(){
    $value = $this->_model->{$this->field};
    return $value == $this->rule;
  }
}
