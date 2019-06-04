<?php
namespace Core\Console;

use Symfony\Component\Console\Application;
use Core\Console\MigrationCommand;
use Core\Console\Migrate;
use Core\Console\ControllerCommand\Controller;
use Core\Foundation\Console\ServerCommand;
class Console extends Application {
    
        public function __construct()
        {
            parent::__construct();
            $this->registerCommands();
        }
        
        public function registerCommands(){
            $this->add(new MigrationCommand());
            $this->add(new Migrate());
            $this->add(new Controller());
            $this->add(new ServerCommand());
        }
}