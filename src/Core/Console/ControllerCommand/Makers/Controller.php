<?php

namespace Core\Console\ControllerCommand\Makers;

// use Symfony\Component\Console\Input\InputOption;
// use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Controller {
    public function __construct(InputInterface $inp,OutputInterface $output)
    {
        $this->filename = $inp->getArgument('classname');
         
    }

    public function generate(){
        $ext = ".php";
        $fullPath = 'app'.DS . $this->filename . $ext;
        $content =
            '<?php
namespace App;


class ' . $this->filename . ' {
         
public function __construct() {

          }


        }
        ';
        $resp = file_put_contents($fullPath, $content . PHP_EOL, FILE_APPEND | LOCK_EX);

        if ($resp) {
            return $this->filename;
        }
        return false;  
    }
}