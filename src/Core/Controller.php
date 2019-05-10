<?php
  namespace Core;
  use Core\Application;
  use Core\View\View;

  class Controller extends Application {
    protected $_controller, $_action;
    public $view, $request;

    public function __construct($controller, $action) {
      parent::__construct();
      $this->_controller = $controller;
      $this->_action = $action;
      $this->request = new Input();
      $this->view = new View();
      $this->onConstruct();
    }

    /**
     * Called when a Controller object is constructed
     * @method onConstruct
     */
    public function onConstruct(){}

    /**
     * used to for a json response
     * @method jsonResponse
     * @param  array        $resp associative array that gets json encoded
     */
    public function jsonResponse($resp){
      header("Access-Control-Allow-Origin: *");
      header("Content-Type: application/json; charset=UTF-8");
      http_response_code(200);
      echo json_encode($resp);
      exit;
    }

  }
