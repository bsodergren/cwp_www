<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Commands\Show;


use CWP\Core\Media;
use CWPCLI\Core\MediaCWP;
use CWPCLI\Traits\Callables;
use CWPCLI\Commands\Show\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Process extends MediaCWP
{
    use Callables;
    use Helper;

    /**
     * meta.
     */
    public $formatter;


    public $commandList = [
      
      
    ];

    public $defaultCommands = [
        'exec' => null,
    ];

    protected $json_file;

    /**
     * __construct.
     *
     * @param mixed $input
     * @param mixed $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::boot($input, $output);

        $this->formatter = new FormatterHelper();
        // Mediatag::$output->getFormatter()->setStyle('id', new OutputFormatterStyle('yellow'));
        // Mediatag::$output->getFormatter()->setStyle('text', new OutputFormatterStyle('green'));
        // Mediatag::$output->getFormatter()->setStyle('error', new OutputFormatterStyle('red'));
        // Mediatag::$output->getFormatter()->setStyle('playlist', new OutputFormatterStyle('bright-magenta'));
        // Mediatag::$output->getFormatter()->setStyle('download', new OutputFormatterStyle('bright-blue'));
        // Mediatag::$output->getFormatter()->setStyle('file', new OutputFormatterStyle('bright-cyan'));

      
        //        dd(IGNORE_NAME_MAP);
    }

    public function exec($option = null)
    {
        MediaCWP::$Table->setHeaders(["Job ID","Close","Job Number"]);
        $table = Media::$explorer->table('media_job');
        $results = $table->fetchAssoc('job_id');
        foreach($results as $row){
            MediaCWP::$Table->addRow([$row['job_id'],$row['close'],$row['job_number']]);
        }
         MediaCWP::$Table->render();


       
       return 1;
   
    }
}
