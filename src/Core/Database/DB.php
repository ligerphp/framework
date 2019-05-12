<?php
namespace Core\Database;
use \PDO;
use \PDOException;

class DB {
  private static $_instance = null;
  private $_pdo, $_query, $_error = false, $_result, $_count = 0, $_lastInsertID = null, $_fetchStyle = PDO::FETCH_OBJ;

  private function __construct() {
    try {
      $this->_pdo = new PDO('mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_NAME'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
      $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch(PDOException $e) {
      die($e->getMessage());
    }
  }

  public static function getInstance() {
    if(!isset(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function query($sql, $params = [],$class = false) {
    $this->_error = false;
    if($this->_query = $this->_pdo->prepare($sql)) {
      $x = 1;
      if(count($params)) {
        foreach($params as $param) {
          $this->_query->bindValue($x, $param);
          $x++;
        }
      }
      if($this->_query->execute()) {
        if($class && $this->_fetchStyle === PDO::FETCH_CLASS){
          $this->_result = $this->_query->fetchAll($this->_fetchStyle,$class);
        } else {
          $this->_result = $this->_query->fetchAll($this->_fetchStyle);
        }
        $this->_count = $this->_query->rowCount();
        $this->_lastInsertID = $this->_pdo->lastInsertId();
      } else {
        $this->_error = true;
      }
    }
    return $this;
  }

  protected function _read($table, $params=[],$class) {
    $columns = '*';
    $joins = "";
    $conditionString = '';
    $bind = [];
    $order = '';
    $limit = '';
    $offset = '';

    //FETCH STYLE
    if(isset($params['fetchStyle'])){
      $this->_fetchStyle = $params['fetchStyle'];
    }

    // conditions
    if(isset($params['conditions'])) {
      if(is_array($params['conditions'])) {
        foreach($params['conditions'] as $condition) {
          $conditionString .= ' ' . $condition . ' AND';
        }
        $conditionString = trim($conditionString);
        $conditionString = rtrim($conditionString, ' AND');
      } else {
        $conditionString = $params['conditions'];
      }
      if($conditionString != '') {
        $conditionString = ' Where ' . $conditionString;
      }
    }

    // columns
    if(array_key_exists('columns',$params)){
      $columns = $params['columns'];
    }

    if(array_key_exists('joins',$params)){
      foreach($params['joins'] as $join){
        $joins .= $this->_buildJoin($join);
      }
      $joins .= " ";
    }

    // bind
    if(array_key_exists('bind', $params)) {
      $bind = $params['bind'];
    }

    // order
    if(array_key_exists('order', $params)) {
      $order = ' ORDER BY ' . $params['order'];
    }

    // limit
    if(array_key_exists('limit', $params)) {
      $limit = ' LIMIT ' . $params['limit'];
    }

    // offset
    if(array_key_exists('offset', $params)) {
      $offset = ' OFFSET ' . $params['offset'];
    }
    $sql = "SELECT {$columns} FROM {$table}{$joins}{$conditionString}{$order}{$limit}{$offset}";
    if($this->query($sql, $bind,$class)) {
      if(!count($this->_result)) return false;
      return true;
    }
    return false;
  }

  public function find($table, $params=[],$class=false) {
    if($this->_read($table, $params,$class)) {
      return $this->results();
    }
    return false;
  }

  public function findFirst($table, $params=[],$class=false) {
    if($this->_read($table, $params,$class)) {
      return $this->first();
    }
    return false;
  }

  public function insert($table, $fields = []) {
    $fieldString = '';
    $valueString = '';
    $values = [];

    foreach($fields as $field => $value) {
      $fieldString .= '`' . $field . '`,';
      $valueString .= '?,';
      $values[] = $value;
    }
    $fieldString = rtrim($fieldString, ',');
    $valueString = rtrim($valueString, ',');
    $sql = "INSERT INTO {$table} ({$fieldString}) VALUES ({$valueString})";
    if(!$this->query($sql, $values)->error()) {
      return true;
    }
    return false;
  }

  public function update($table, $id, $fields = []) {
    $fieldString = '';
    $values = [];
    foreach($fields as $field => $value) {
      $fieldString .= ' ' . $field . ' = ?,';
      $values[] = $value;
    }
    $fieldString = trim($fieldString);
    $fieldString = rtrim($fieldString, ',');
    $sql = "UPDATE {$table} SET {$fieldString} WHERE id = {$id}";
    if(!$this->query($sql, $values)->error()) {
      return true;
    }
    return false;
  }

  public function delete($table, $id) {
    $sql = "DELETE FROM {$table} WHERE id = {$id}";
    if(!$this->query($sql)->error()) {
      return true;
    }
    return false;
  }

  public function results() {
    return $this->_result;
  }

  public function first() {
    return (!empty($this->_result))? $this->_result[0] : [];
  }

  public function count() {
    return $this->_count;
  }

  public function lastID() {
    return $this->_lastInsertID;
  }

  public function get_columns($table) {
    return $this->query("SHOW COLUMNS FROM {$table}")->results();
  }

  public function error() {
    return $this->_error;
  }

  protected function _buildJoin($join=[]){
    $table = $join[0];
    $condition = $join[1];
    $alias = $join[2];
    $type = (isset($join[3]))? strtoupper($join[3]) : "INNER";
    $jString = "{$type} JOIN {$table} {$alias} ON {$condition}";
    return " " . $jString;
  }
}
