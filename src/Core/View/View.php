<?php
namespace Core\View;


  class View {
    protected $_siteTitle = SITE_TITLE,  $_layout = DEFAULT_LAYOUT;
    protected $_content=[], $_currentBuffer;
    

    /**
     * used to render the layout and view
     * @method render
     * @param  string $viewName path to view
     */
    public function render($viewName) {
      
// $loader = new Twig\Loader\FilesystemLoader(ROOT . '/app/views');
// $twig = new Twig\Environment($loader, [
//     'cache' => '/bootstrap/cache/compilation_cache',
// ]);
// $template = $twig->load($viewName);
// echo $template->render(['the' => 'variables', 'go' => 'here']); 

      $viewAry = explode('/', $viewName);
      $viewString = implode(DS, $viewAry);
      if(file_exists(ROOT . DS . 'app' . DS . 'views' . DS . $viewString . '.php')) {
        include(ROOT . DS . 'app' . DS . 'views' . DS . $viewString . '.php');
        include(ROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . $this->_layout . '.php');

      } else {
        // dd(ROOT .DS. 'app' . DS . 'views' . DS . $viewString . '.php');
        die('The view \"' . $viewName . '\" does not exist.');
      }
    }

    /**
     * Used in the layouts to embed the head and body
     * @method content
     * @param  string  $type can be head or body
     * @return string       returns the output buffer of head and body
     */
    public function content($type) {
      if(array_key_exists($type,$this->_content)){
        return $this->_content[$type];
      } else {
        return false;
      }
    }

    /**
     * starts the output buffer for the head or body
     * @method start
     * @param  string $type can be head or body
     */
    public function start($type) {
      if(empty($type)) die('you must define a type');
      $this->_currentBuffer = $type;
      ob_start();
    }

    /**
     * echos the output buffer in the layout
     * @method end
     * @return string rendered html for head or body
     */
    public function end() {
      if(!empty($this->_currentBuffer)){
        $this->_content[$this->_currentBuffer] = ob_get_clean();
        $this->_currentBuffer = null;
      } else {
        die('You must first run the start method.');
      }
    }
    /**
     * Getter for the site title
     * @method siteTitle
     * @return string    site title set in the view object
     */
    public function siteTitle() {
      return $this->_siteTitle;
    }

    /**
     * Sets the page title
     * @method setSiteTitle
     * @param  string   $title used for the title
     */
    public function setSiteTitle($title) {
      $this->_siteTitle = $title;
    }

    /**
     * sets the layout to be loaded
     * @method setLayout
     * @param  string    $path name of layout
     */
    public function setLayout($path) {
      $this->_layout = $path;
    }

    /**
     * inserts a partial into another partial
     * @method insert
     * @param  string $path path to view example register/register
     */
    public function insert($path){
      include ROOT . DS . 'app' . DS . 'views' . DS . $path . '.php';
     }

    /**
     * inserts a partial into a view
     * @method partial
     * @param  string  $group   view sub directory
     * @param  string  $partial partial name
     */
    public function partial($group, $partial){
      include ROOT . DS . 'app' . DS . 'views' . DS . $group . DS . 'partials' . DS . $partial . '.php';
    }

  }
