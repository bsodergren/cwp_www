<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWP\Utils;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * ScriptWriter.
 */
class ScriptWriter
{
    /**
     * script_text.
     */
    public $script_text;


    /**
     * script.
     */
    public $script;

    public $directory;

    /**
     * fileListAray.
     *
     * @var array
     */
    public $fileListAray = [];

    public static function addPattern($tableName)
    {
        $class = ucfirst($tableName);
        $Pattern_file = __PUBLIC_ROOT__.DIRECTORY_SEPARATOR."src\App\Database\Map".\DIRECTORY_SEPARATOR.$class.'.php';


        if (!file_exists($Pattern_file)) {
            $finder = new Finder();
            $filesystem = new Filesystem();

            $finder->files()->in(__CONFIG_ROOT__)->name('Patterns_template.txt');
            foreach ($finder as $file) {
                $name = $file->getFilenameWithoutExtension();
                ${$name} = $file->getContents();
                // $output->writeln($$name );
                // ...
            }
            $command_array = [
                'CLASSNAME' => $class,
                'TABLE' => strtolower($class),

            ];
            foreach ($command_array as $key => $value) {
                $key = '%%'.strtoupper($key).'%%';
                if (null != $value) {
                    $Patterns_template = str_replace($key, $value, $Patterns_template);
                }
            }
            $filesystem->dumpFile($Pattern_file, $Patterns_template);
        }
    }
}
