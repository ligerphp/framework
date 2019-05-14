<?php 
namespace Core\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Core\DB;
use Core\Console\ControllerCommand\Makers\Controller;

class Command extends SymfonyCommand
{

    public function __construct()
    {
        parent::__construct();
        $this->isCli = php_sapi_name() == 'cli';
        if(!RUN_MIGRATIONS_FROM_BROWSER && !$this->isCli) die('restricted');
        
    }

    public function createController(InputInterface $input, OutputInterface $output){
      $con =   new Controller($input,$output);
      if($filename = $con->generate()){
          $output->writeln('File successfully created.. '.$filename);
      }else{
        $output->writeln('Controller file was not created.. '.$filename);

      }

    }

    public function migrate(InputInterface $input, OutputInterface $output){
        
        $db = DB::getInstance();
        
    $sql = "CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT,
        PRIMARY KEY (id),migration VARCHAR(200) NOT NULL
        )  ENGINE=INNODB;";
      $res = !$db->query($sql)->error();
      if(!$res){
          $this->error = true;
      }
      
    $migrationTable = $db->query("SHOW TABLES LIKE 'migrations'")->results();
    $previousMigs = [];
    $migrationsRun = [];

  if(!empty($migrationTable)){
    $query = $db->query("SELECT migration FROM migrations")->results();
    foreach($query as $q){
      $previousMigs[] = $q->migration;
    }
  } 
   
  
  // get all files
  $migrations = glob('database'. DS . 'migrations' . DS . '*.php');


  foreach($migrations as $fileName){
    $klass = str_replace('database' . DS . 'migrations' . DS ,'',$fileName);
    $klass = str_replace('.php','',$klass);
    
    if(!in_array($klass,$previousMigs)){
      $migrationClass = 'Database\Migrations\\'.$klass;
      $mig = new $migrationClass($this->isCli);
      $mig->up();
      $db->insert('migrations',['migration'=>$klass]);
      $migrationsRun[] = $migrationClass;
      $this->errors = $mig->errors;

    }else{
        $output->writeln('Migration failed becuase of a previous migrations done.');
        die;
    }   
  }

  if(sizeof($migrationsRun) == 0){
    if($this->isCli){
      $output->writeln("No new Miigrations to run");
    } 
  }else if(!$this->errors){
      $output->writeln('Migrations was performed successful..');
  }

    }

    protected function newMigration($filename)
    {
        $_fileName = 'Migration'.uniqid() . '_' . $filename;
        $ext = ".php";
        $fullPath = 'database' . DS . 'migrations' . DS . $_fileName . $ext;
        $content =
            '<?php
namespace Database\Migrations;
use Core\Migration;


class ' . $_fileName . ' extends Migration {
         
public function up() {

          }

public function down(){
        //code to destroy migration
        
    }
        }
        ';
        $resp = file_put_contents($fullPath, $content . PHP_EOL, FILE_APPEND | LOCK_EX);

        if ($resp) {
            return true;
        }
        return false;
    }
    protected function createMigration(InputInterface $input, OutputInterface $output)
    {
        $m = $this->newMigration($input->getArgument('classname'));

        $outputStyle = new OutputFormatterStyle('red', 'yellow', ['bold', 'blink']);
        $output->getFormatter()->setStyle('fire', $outputStyle);

        $output->writeln('<fire>Migration is runnig...</>');

        if ($m) {
            $output->writeln('<fire>Migration File was created successfully...</>');
        } else {
            $output->writeln('<fire>Migration File was not created</>');

        }

    }
    protected function greetUser(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            '====**** User Greetings Console App ****====',
            '==========================================',
            '',
        ]);

        // outputs a message without adding a "\n" at the end of the line
        $output->write($this->getGreeting() . ', ' . $input->getArgument('username'));
    }
    private function getGreeting()
    {
        /* This sets the $time variable to the current hour in the 24 hour clock format */
        $time = date("H");
        /* Set the $timezone variable to become the current timezone */
        $timezone = date("e");
        /* If the time is less than 1200 hours, show good morning */
        if ($time < "12") {
            return "Good morning";
        } else
        /* If the time is grater than or equal to 1200 hours, but less than 1700 hours, so good afternoon */
        if ($time >= "12" && $time < "17") {
            return "Good afternoon";
        } else
        /* Should the time be between or equal to 1700 and 1900 hours, show good evening */
        if ($time >= "17" && $time < "19") {
            return "Good evening";
        } else
        /* Finally, show good night if the time is greater than or equal to 1900 hours */
        if ($time >= "19") {
            return "Good night";
        }
    }
}
