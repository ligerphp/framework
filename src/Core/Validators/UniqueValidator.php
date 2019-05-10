<?php
namespace Core\Validators;
use Core\Validators\CustomValidator;
use Core\H;

class UniqueValidator extends CustomValidator{

  public function runValidation(){
    $value = $this->_model->{$this->field};

    if($value == '' || !isset($value)){
      // this allows unique validator to be used with empty strings for fields that are not required.
      return true;
    }

    $conditions = ["{$this->field} = ?"];
    $bind = [$value];

    //check updating record
    if(!empty($this->_model->id)){
      $conditions[] = "id != ?";
      $bind[] = $this->_model->id;
    }

    //this allows you to check multiple fields for Unique
    foreach($this->additionalFieldData as $adds){
      $conditions[] = "{$adds} = ?";
      $bind[] = $this->_model->{$adds};
    }
    $queryParams = ['conditions'=>$conditions,'bind'=>$bind];
    $other = $this->_model::findFirst($queryParams);
    return(!$other);
  }
}
