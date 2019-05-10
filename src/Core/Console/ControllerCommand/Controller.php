<?php 
namespace Core\Console\ControllerCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Core\Src\Command;

class Controller extends Command
{
    
    public function configure()
    {
        $this -> setName('make:controller')
            -> setDescription('Create new controller file.')
            -> setHelp('This command allows you to generate new controller files.')
            -> addArgument('classname', InputArgument::REQUIRED, 'The classname for the file.');
    }
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createController($input, $output);
    }
}