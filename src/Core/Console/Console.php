<?php
namespace Core\Console;

use Symfony\Component\Console\Application;
use Core\Src\MigrationCommand;
use Core\Src\Migrate;
use Core\Src\ControllerCommand\Controller;

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
        }
}