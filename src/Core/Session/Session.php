<?php
namespace Core\Session;

class Session {

  /**
   * 
   * Current session driver could be file,redis,cookie
   */
  protected $driver;

  /**
   * All configuration for sessions
   */
  protected $sessionConfig;

  /**
   * Session file location incase driver is set to file
   * 
   */
  protected $file;

      /*
    |--------------------------------------------------------------------------
    | Session Database Table
    |--------------------------------------------------------------------------
    |
    | When using the "database" session driver, you may specify the table we
    | should use to manage the sessions. Of course, a sensible default is
    | provided for you; however, you are free to change this as needed.
    |
    */
  protected $table;


  public function __construct($driver = '')
  {
      if(file_exists(ROOT . DS . 'config' . DS . 'sessions.php')){
       $this->sessionConfig =  include ROOT .DS . 'config' . DS . 'sessions.php';
      };
      
    $this->driver = $driver != '' ? $driver : $this->sessionConfig['driver'];
    $this->lifetime = $this->sessionConfig['lifetime'];
    $this->file = $this->sessionConfig['files'];
    $this->table = $this->sessionConfig['table'];
    $this->session_file  =   file_get_contents(ROOT .DS .'storage' . DS .'session'. DS .'sessions.json');


  }
  /**
   * Check if a session exists
   */
  public function exists($name) {


    if($this->driver == 'web'){
  
      return (isset($_SESSION[$name])) ? true : false;
    
    } else if($this->driver  == 'file'){

       $sessions =  json_encode($this->session_file);
        $sessionToFind =  $sessions->$name ? true : false;   
        return $sessionToFind;
      }else if($this->driver == 'cookie'){

      }

    
  }

  /**
   * Get an existing session by name
   */
  public function get($name) {
    return $_SESSION[$name];
  }

  public function set($name, $value) {
    if($this->driver == 'file'){
      json_encode($this->session_file);
    }

    return $_SESSION[$name] = $value;
  }

  public function delete($name) {
    if($this->exists($name)) {
      unset($_SESSION[$name]);
    }
  }

  public function uagent_no_version() {
    $uagent = $_SERVER['HTTP_USER_AGENT'];
    $regx = '/\/[a-zA-Z0-9.]+/';
    $newString = preg_replace($regx, '', $uagent);
    return $newString;
  }

  /**
   * Adds a session alert message
   * @method addMsg
   * @param  string $type can be info, success, warning or danger
   * @param  string $msg  the message you want to display in the alert
   */
  public  function addMsg($type,$msg){
    $sessionName = 'alert-' .  $type;
    $this->set($sessionName,$msg);
  }

  public  function displayMsg(){
    $alerts = ['alert-info','alert-success','alert-warning','alert-danger','alert-primary','alert-secondary','alert-dark','alert-light'];
    $html = '';
    foreach($alerts as $alert){
      if($this->exists($alert)){
        $html .= '<div class="alert '. $alert .' alert-dismissible" role="alert">';
        $html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        $html .= $this->get($alert);
        $html .= '</div>';
        $this->delete($alert);
      }
    }
    return $html;
  }
}
