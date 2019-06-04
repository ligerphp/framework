<?php
namespace Core\Foundation\Console;

use Symfony\Component\Console\Command\Command as Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApplicationMaker extends Command {

/**
 * Start the applications server
 * 
 */
    public function kickoff(InputInterface $input, OutputInterface $output){

        $directory = getcwd();
   
      $port =   $input->getArgument('port') ? $input->getArgument('port') : 6060;
        //perform processes
        $process = new Process('php -S localhost:"'.$port.'" -t=public', $directory, null, null, null);
        
        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }
        $output->writeln('<comment>Your Liger Application is running on localhost:6060</comment>');

        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });


    }
}