<?php
namespace Core\Database\Model;

use Core\Database\DB;
/**
 * Parent class for App Models
 */
class Model {
  
  protected $_modelName, $_validates=true,$_validationErrors=[];
  public $id;
  protected static $_db, $_table, $_softDelete=false;

  public function __construct() {
    $this->_modelName = str_replace(' ', '', ucwords(str_replace('_',' ', static::$_table)));
    $this->onConstruct();
  }

  public static function getDb(){
    if(!self::$_db) {
      self::$_db = DB::getInstance();
    }
    return self::$_db;
  }

  /**
   * query database table for model to get column information
   * @method get_columns
   * @return object      columns object
   */
  public static function get_columns() {
    return static::getDb()->get_columns(static::$_table);
  }

  /**
   * gets an associative array of field values for insert or updating
   * @method getColumnsForSave
   * @return array            associative array of fields from database and values from model object
   */
  public function getColumnsForSave(){
    $columns = static::get_columns();
    $fields = [];
    foreach($columns as $column){
      $key = $column->Field;
      $fields[$key] = $this->{$key};
    }
    return $fields;
  }

  /**
   * adds to the conditions to avoid getting soft deleted rows returned
   * @method _softDeleteParams
   * @param  array            $params  defined parameters to search by
   * @return array            $params  parameters with appended conditions for soft delete
   */
  protected static function _softDeleteParams($params){
    if(static::$_softDelete){
      if(array_key_exists('conditions',$params)){
        if(is_array($params['conditions'])){
          $params['conditions'][] = "deleted != 1";
        } else {
          $params['conditions'] .= " AND deleted != 1";
        }
      } else {
        $params['conditions'] = "deleted != 1";
      }
    }
    return $params;
  }

  /**
   * used to set default fetchStyle param
   * @method _fetchStyleParams
   * @param  array            $params query params
   * @return array                    updated params
   */
  protected static function _fetchStyleParams($params){
    if(!isset($params['fetchStyle'])){
      $params['fetchStyle'] = \PDO::FETCH_CLASS;
    }
    return $params;
  }

  /**
   * Find a result set
   * @method find
   * @param  array  $params conditions
   * @return array          array of rows or an empty array if none found
   */
  public static function find($params = []) {
    $params = static::_fetchStyleParams($params);
    $params = static::_softDeleteParams($params);
    $resultsQuery = static::getDb()->find(static::$_table, $params,static::class);
    if(!$resultsQuery) return [];
    return $resultsQuery;
  }

  /**
   * Find the first object that matches the conditions
   * @method findFirst
   * @param  array     $params array of conditions and binds
   * @return object | false      returns Model object or false if one is not found
   */
  public static function findFirst($params = []) {
    $params = static::_fetchStyleParams($params);
    $params = static::_softDeleteParams($params);
    $resultQuery = static::getDb()->findFirst(static::$_table, $params,static::class);
    return $resultQuery;
  }

  /**
   * Finds a row for this model by id
   * @method findById
   * @param  integer   $id id of the object to return
   * @return object        Model Object
   */
  public static function findById($id) {
    return static::findFirst(['conditions'=>"id = ?", 'bind' => [$id]]);
  }

  /**
   * Save the current properties to the database
   * @method save
   * @return boolean
   */
  public function save() {
    $this->validator();
    $save = false;
    if($this->_validates){
      $this->beforeSave();
      $fields = $this->getColumnsForSave();
      // determine whether to update or insert
      if($this->isNew()) {
        $save = $this->insert($fields);
        // populate object with the id
        if($save){
          $this->id = static::getDb()->lastID();
        }
      } else {
        $save = $this->update($fields);
      }
      // run after save
      if($save){
        $this->afterSave();
      }
    }
    return $save;
  }

  /**
   * Insert a row into the database
   * @method insert
   * @param  array $fields associative array ['field'=>'value']
   * @return boolean       returns if the insert was successful
   */
  public function insert($fields) {
    if(empty($fields)) return false;
    if(array_key_exists('id', $fields)) unset($fields['id']);
    return static::getDb()->insert(static::$_table, $fields);
  }

  /**
   * Update a row in the database
   * @method update
   * @param  array $fields associative array of fields to update ['field'=>'value']
   * @return boolean       if the update was successful
   */
  public function update($fields) {
    if(empty($fields) || $this->id == '') return false;
    return static::getDb()->update(static::$_table, $this->id, $fields);
  }

  /**
   * Delete a row in the database, could also be soft delete
   * @method delete
   * @return boolean     [description]
   */
  public function delete() {
    if($this->id == '' || !isset($this->id)) return false;
    $this->beforeDelete();
    if(static::$_softDelete) {
      $deleted = $this->update(['deleted' => 1]);
    } else {
      $deleted = static::getDb()->delete(static::$_table, $this->id);
    }
    $this->afterDelete();
    return $deleted;
  }

  /**
   * Used to run a manual query on this model's table
   * @method query
   * @param  [type] $sql  [description]
   * @param  array  $bind [description]
   * @return [type]       [description]
   */
  public function query($sql, $bind=[]) {
    return static::getDb()->query($sql, $bind);
  }

  /**
   * Returns an object with only the properties set. Removes all methods. Can be used to save memory for large datasets.
   * @method data
   * @return object
   */
  public function data() {
    $data = new \stdClass();
    foreach(static::get_columns() as $column) {
      $columnName = $column->Field;
      $data->{$columnName} = $this->{$columnName};
    }
    return $data;
  }

  /**
   * Update the object with an associative array
   * @method assign
   * @param  array   $params    associative array of values to update ['property'=>'new value']
   * @param  array   $list      (optional) indexed array of keys that are to be validated against
   * @param  boolean $blackList (optional) if blacklist is set to true the list param will be treated like a blacklist else it will be treated like a whitelist
   * @return object             returns a model object allows for chaining.
   */
  public function assign($params,$list=[],$blackList=true) {
    foreach($params as $key => $val) {
      // check if there is permission to update the object
      $whiteListed = true;
      if(sizeof($list) > 0){
        if($blackList){
          $whiteListed = !in_array($key,$list);
        } else {
          $whiteListed = in_array($key,$list);
        }
      }
      if(property_exists($this,$key) && $whiteListed){
        $this->$key = $val;
      }
    }
    return $this;
  }

  /**
   * Runs a validator object and sets validates boolean and adds error message if validator fails
   * @method runValidation
   * @param  object        $validator Validator Object
   */
  public function runValidation($validator){
    $key = $validator->field;
    if(!$validator->success){
      $this->addErrorMessage($key,$validator->msg);
    }
  }

  /**
   * A getter for the model object validation errors
   * @method getErrorMessages
   * @return array           returns an empty array for no errors or an associative array for errors ['field'=>'msg']
   */
  public function getErrorMessages(){
    return $this->_validationErrors;
  }

  /**
   * Getter for _validates can be used to see if validation passed
   * @method validationPassed
   * @return boolean          validation true means validation passed
   */
  public function validationPassed(){
    return $this->_validates;
  }

  /**
   * Method used to add an error message to the model object
   * @method addErrorMessage
   * @param  string          $field property to add the error on. This should match the form field
   * @param  string          $msg   error message to display to the user
   */
  public function addErrorMessage($field,$msg){
    $this->_validates = false;
    if(array_key_exists($field,$this->_validationErrors)){
      $this->_validationErrors[$field] .= " " . $msg;
    } else {
      $this->_validationErrors[$field] = $msg;
    }
  }

  /**
   * Method that is called before delete
   * @method beforeDelete
   */
  public function beforeDelete(){}

  /**
   * Method that is called after delete
   * @method afterDelete
   */
  public function afterDelete(){}

  /**
   * Method that is called before save
   * @method beforeSave
   */
  public function beforeSave(){}

  /**
   * Method that is called after save
   * @method afterSave
   */
  public function afterSave(){}

  /**
   * Method that is called on save if validation fails the save function will not proceed
   * @method validator
   */
  public function validator(){}

  /**
   * Runs when the object is constructed
   * @method onConstruct
   */
  public function onConstruct(){}

  /**
   * Populates the appropriate time stamps for created_at and updated_at
   * @method timeStamps
   */
  public function timeStamps(){
    $dt = new \DateTime("now", new \DateTimeZone("UTC"));
    $now = $dt->format('Y-m-d H:i:s');
    $this->updated_at = $now;
    if($this->isNew()){
      $this->created_at = $now;
    }
  }

  /**
   * Determine if the object is a new insertion or previous record
   * @method isNew
   * @return boolean returns true if there isn't an id false if there is one
   */
  protected function isNew(){
    return (property_exists($this,'id') && !empty($this->id))? false : true;
  }

}
