<?php
namespace Core\Foundation\Console;


use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class ServerCommand extends ApplicationMaker {

    /**
     * is instance from cli
     * 
     */
    protected $isCli;

    public function __construct(){
        parent::__construct();
        $this->isCli = php_sapi_name() == 'cli';
        if(!RUN_MIGRATIONS_FROM_BROWSER && !$this->isCli) die('restricted');
        
    }

    public function configure(){
        $this->setName('go')
            ->setDescription('Start the liger Application.')
            ->setHelp('This command allows you to start a web server for your application..')
            ->addArgument('port', InputArgument::OPTIONAL, 'Development server port',6060);
    }


    public function execute(InputInterface $input, OutputInterface $output){
        $this->kickoff($input, $output);
    }



}