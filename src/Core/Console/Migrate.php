<?php
namespace Core\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Core\Console\Command;

class Migrate extends Command
{
    
    public function configure()
    {
        $this -> setName('migrate')
            -> setDescription('Command used to create a new migration file.')
            -> setHelp('This command allows you to generate new migration files..')
            -> addArgument('flag', InputArgument::OPTIONAL, 'migration flags');
    }
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this ->migrate($input, $output);
    }
}