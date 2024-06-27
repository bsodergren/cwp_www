<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Core;

use CWP\Core\MediaStopWatch;
use CWPCLI\Utilities\Option;
use CWPCLI\Traits\CmdProcess;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class MediaCWP extends Command
{
    use CmdProcess;

    public static $Console;
    public static $Table;




    public static $output;

    public $command;

    public static $input;



    public function __construct(InputInterface $input = null, OutputInterface $output = null, $args = null)
    {
        self::boot($input, $output);

       
        
    }


    public function boot(InputInterface $input = null, OutputInterface $output = null)
    {

        $this->command = self::getDefaultName();
     
        MediaCache::init($input, $output);

        Option::init($input);
        self::$Console = new ConsoleOutput();
        self::$Table = new Table($output);

       
    }

    public function process()
    {
        $ClassCmds = $this->runCommand();

        foreach ($ClassCmds as $cmd => $option) {
            if (method_exists($this, $cmd)) {
                $this->{$cmd}($option);
            } else {
                self::$output->writeln('<info>'.$cmd.' doesnt exist</info>');

                return 0;
            }
        }
    }


    public function exec($option = null) {}

    public function print() {}

}
