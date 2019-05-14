<?php 
namespace Core\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Core\Console\Command;

class MigrationCommand extends Command
{
    
    public function configure()
    {
        $this -> setName('make:migration')
            -> setDescription('Command used to create a new migration file.')
            -> setHelp('This command allows you to generate new migration files..')
            -> addArgument('classname', InputArgument::REQUIRED, 'The classname of the migration.');
    }
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this -> createMigration($input, $output);
    }
}