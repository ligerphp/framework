<?php
namespace Core\Foundation;

use Symfony\Component\Routing\Route;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Routing\RouteCollection;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\InvalidPathException;
use Core\Container\Container;
use Core\Http\Request;

class Application extends Container {
    
   public $registerPath = [];

       /**
     * The framework's version.
     *
     * @var string
     */
    const VERSION = '1.0.0';

        /**
     * The base path for the liger installation.
     *
     * @var string
     */
    protected $basePath;
        /**
     * The custom database path defined by the developer.
     *
     * @var string
     */
    protected $databasePath;

    /**
     * The custom storage path defined by the developer.
     *
     * @var string
     */
    protected $storagePath;

    /**
     * The custom environment path defined by the developer.
     *
     * @var string
     */
    protected $environmentPath;

    /**
     * The environment file to load during bootstrapping.
     *
     * @var string
     */
    protected $environmentFile = '.env';

        /**
     * All of the registered service providers.
     *
     * @var array
     */
    protected $serviceProviders = [];

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */

    protected $loadedProviders = [];

    public static $instance;
    public $webRoutes,$apiRoutes;
    public function __construct($basePath='')
    {
        if ($basePath) {
          $this->setBasePath($basePath);
      }

        $this->routes = new RouteCollection();
        $this->routes->setMethods(['POST','GET','DELETE','PATCH','UPDATE','HEAD','OPTIONS']);
        $this->loadRouteFiles();
      require  $this->configPath('config.php');
    }

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    
    /**
     * Set the base path for the application.
     *
     * @param  string  $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath =  $basePath;

        return $this->basePath;
    }
       
    /**
     * Get the path to the application "app" directory.
     *
     * @param  string  $path Optionally, a path to append to the app path
     * @return string
     */
    public function path($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'app'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the base path of the liger's installation.
     *
     * @param  string  $path Optionally, a path to append to the base path
     * @return string
     */
    public function basePath($path = '')
    {
        return $this->basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
     /**
     * Get the path to the bootstrap directory.
     *
     * @param  string  $path Optionally, a path to append to the bootstrap path
     * @return string
     */
    public function bootstrapPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'bootstrap'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the application configuration files.
     *
     * @param  string  $path Optionally, a path to append to the config path
     * @return string
     */
    public function configPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'config'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the database directory.
     *
     * @param  string  $path Optionally, a path to append to the database path
     * @return string
     */
    public function databasePath($path = '')
    {
        return ($this->databasePath ?: $this->basePath.DIRECTORY_SEPARATOR.'database').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Set the database directory.
     *
     * @param  string  $path
     * @return $this
     */
    public function useDatabasePath($path)
    {
        $this->databasePath = $path;

        $this->instance('path.database', $path);

        return $this;
    }


    /**
     * Get the path to the public / web directory.
     *
     * @return string
     */
    public function publicPath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'public';
    }

    /**
     * Get the path to the storage directory.
     *
     * @return string
     */
    public function storagePath()
    {
        return $this->storagePath ?: $this->basePath.DIRECTORY_SEPARATOR.'storage';
    }

     

    /**
     * Set the storage directory.
     *
     * @param  string  $path
     * @return $this
     */
    public function useStoragePath($path)
    {
        $this->storagePath = $path;

        $this->instance('path.storage', $path);

        return $this;
    }

    /**
     * Get the path to the resources directory.
     *
     * @param  string  $path
     * @return string
     */
    public function resourcePath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'resources'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the environment file directory.
     *
     * @return string
     */
    public function environmentPath()
    {
        return $this->environmentPath ?: $this->basePath;
    }

    /**
     * Set the directory for the environment file.
     *
     * @param  string  $path
     * @return $this
     */
    public function useEnvironmentPath($path)
    {
        $this->environmentPath = $path;

        return $this;
    }

    /**
     * Set the environment file to be loaded during bootstrapping.
     *
     * @param  string  $file
     * @return $this
     */
    public function loadEnvironmentFrom($file)
    {
        $this->environmentFile = $file;

        return $this;
    }

    /**
     * set the route files to be loaded during bootstrapping
     * 
     * 
     */
    public function loadRouteFiles(){
      $this->webRoutes =  $this->basePath('routes/web.php');
      $this->apiRoutes =  $this->basePath('routes/api.php');
        
    }

    /**
     * Get Routes
     */
    public function routes(){

        if(file_exists($this->webRoutes) && file_exists($this->apiRoutes)){
            $app = $this;
            include $this->webRoutes;
            include $this->apiRoutes;

        }else{
            dd($this->webRoutes);
        }
    }
    /**
     * Get the environment file the application is using.
     *
     * @return string
     */
    public function environmentFile()
    {
        return $this->environmentFile ?: '.env';
    }

    /**
     * Get the fully qualified path to the environment file.
     *
     * @return string
     */
    public function environmentFilePath()
    {
        return $this->environmentPath().DIRECTORY_SEPARATOR.$this->environmentFile();
    }

    /**
     * Get or check the current application environment.
     *
     * @return string|bool
     */
    public function environment()
    {
        if (func_num_args() > 0) {
            $patterns = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

            return Str::is($patterns, $this['env']);
        }

        return $this['env'];
    }
    
    public static function getInstance(){
        // Check if instance is already exists 		
		if(self::$instance == null) {
			self::$instance = new Application(ROOT);
		}
		
		return self::$instance;
    }
    /**
     * Determine if application is in local environment.
     *
     * @return bool
     */
    public function isLocal()
    {
        return $this['env'] == 'local';
    }
    private function _set_reporting() {
        if(DEBUG && php_sapi_name() != 'cli') {
          error_reporting(E_ALL);
          ini_set('display_errors', 1);
        } else {
          error_reporting(0);
          ini_set('display_errors', 0);
          ini_set('log_errors', 1);
          ini_set('error_log', ROOT . DS .'tmp' . DS . 'logs' . DS . 'errors.log');
        }
      }

    public function registerRoute($path,$controller,$method){
      $route = new Route($path, ['_controller' => $controller],array(),array(),'',array(),[$method,'HEAD']);
      $this->routes->add(uniqid().time(), $route);
    }


    public function post($path,$controller){
      $this->registerRoute($path,$controller,'POST');
    }

    public function get($path,$controller){
      $this->registerRoute($path,$controller,'GET');
    }

    public function put($path,$controller){
      $this->registerRoute($path,$controller,'PUT');
    }

    public function update($path,$controller){
        $this->registerRoute($path,$controller,'UPDATE');
      }
    /**
     * Create a delete route
     * @param $path required string
     * @return void
     * @throws NotFound when route does not match
     */
    public function delete($path,$controller){
      $this->registerRoute($path,$controller,'DELETE');
    }
    

    public function start(){
        if(php_sapi_name() == 'cli'){
            $this->loadEnvironment();
            $this->loadContainerForConsole();
        }else{
            
     parent::__construct($this->routes);
     $this->loadEnvironment();   
     $response =  $this->loadContainer();
     $response->send();

        }
    }
public function loadContainerForConsole(){
         $this->instantiate('app',$this);
        $this->instantiate('session',\Core\Session\Session::class);
        $this->instantiate('form',\Core\Foundation\FormHelpers::class);
        
}

    public function loadContainer(){

        $this->instantiate('app',$this);
        $this->instantiate('session',\Core\Session\Session::class);
        $this->instantiate('form',\Core\Foundation\FormHelpers::class);
        
        $request = $this->getContainer()->get('request')->createFromGlobals();
        $response = $this->getContainer()->get('framework')->handle($request);
        
        return $response;
    }

    /**
     * 
     * Let's load the environment variables from .env files
     */
    public function loadEnvironment(){

try {

$dotenv = new Dotenv();
$dotenv->load($this->basePath('/').'.env');

} catch (InvalidPathException $e) {
    die('The environment path could not be found: '.$e->getMessage());

} catch (InvalidFileException $e) {
    die('The environment file is invalid: '.$e->getMessage());
}

    }


    public static function addTocontainer($name){

    }
    
}