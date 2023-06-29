<?php
class Utils
{

	public static function get_filelist($directory, $ext = 'log', $skip_files = 0)
    {
        $files_array = [];

        if (is_dir($directory)) {
            $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
            foreach ($rii as $file) {
                if ($file->isDir()) {
                    continue;
                }
                $filename = $file->getPathname();
                $filename = \Nette\Utils\FileSystem::normalizePath($filename);
                if (preg_match('/(' . $ext . ')$/', $filename)) {
                    if ($skip_files == 1) {
                        if (!self::skipFile($filename)) {
                            $files_array[] = $filename;
                        }
                    } else {
                        $files_array[] = $filename;
                    }
                }
            }
        }

        return $files_array;
    }

	

    public static function skipFile($filename)
    {
        $f = fopen($filename, 'r');
        $line = fgets($f);
        fclose($f);
        return strpos($line, '#skip');
    }



    public static function setSkipFile($filename)
    {

        if (!self::skipFile($filename)) {
            $replacement = '<?php';
            $replacement .= ' #skip';
            $__db_string = FileSystem::read($filename);
            $__db_write = str_replace('<?php', $replacement, $__db_string);
            FileSystem::write($filename, $__db_write);
        }
    }

	
    public static function toint($string)
	{
		
		$string_ret = str_replace(",","",$string);
		return $string_ret;
	}
}


