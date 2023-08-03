<?php

 function prepareWindowsCommandLine(string $cmd, array $env): string
    {
        $uid = uniqid('', true);
        $cmd = preg_replace_callback(
            '/"(?:(
                [^"%!^]*+
                (?:
                    (?: !LF! | "(?:\^[%!^])?+" )
                    [^"%!^]*+
                )++
            ) | [^"]*+ )"/x',
            function ($m) use (&$env, $uid) {
                static $varCount = 0;
                static $varCache = [];
                if (!isset($m[1])) {
                    return $m[0];
                }
                if (isset($varCache[$m[0]])) {
                    return $varCache[$m[0]];
                }
                if (str_contains($value = $m[1], "\0")) {
                    $value = str_replace("\0", '?', $value);
                }
                if (false === strpbrk($value, "\"%!\n")) {
                    return '"'.$value.'"';
                }

                $value = str_replace(['!LF!', '"^!"', '"^%"', '"^^"', '""'], ["\n", '!', '%', '^', '"'], $value);
                $value = '"'.preg_replace('/(\\\\*)"/', '$1$1\\"', $value).'"';
                $var = $uid.++$varCount;

                $env[$var] = $value;

                return $varCache[$m[0]] = '!'.$var.'!';
            },
            $cmd
        );

        $cmd = 'cmd /V:ON /E:ON /D /C ('.str_replace("\n", ' ', $cmd).')';

        return $cmd;
    }
function disable_ob() {
    // Turn off output buffering
    ini_set('output_buffering', 'off');
    // Turn off PHP output compression
    ini_set('zlib.output_compression', false);
    // Implicitly flush the buffer(s)
    ini_set('implicit_flush', true);
    ob_implicit_flush(true);
    // Clear, and turn off output buffering
    while (ob_get_level() > 0) {
        // Get the curent level
        $level = ob_get_level();
        // End the buffering
        ob_end_clean();
        // If the current level has not changed, abort
        if (ob_get_level() == $level) break;
    }
    // Disable apache output buffering/compression
    if (function_exists('apache_setenv')) {
        apache_setenv('no-gzip', '1');
        apache_setenv('dont-vary', '1');
    }
}


function composerHeader()
{
echo " <script>";
echo " setTimeout(function () { window.location.href = 'index.php'; }, 5);";
echo " </script>";
}
function flushDummy()
{
    $flushdummy = '';
    for ($i = 0; $i < 1200; ++$i) {
        $flushdummy = $flushdummy.'      ';
    }

    return $flushdummy;
}
function push($contents)
{
    echo $contents, flushDummy(),"<br>";
    flush();
    @ob_flush();
}


function runComposer()
{
    $composer_exe = 'php '.__PROJECT_ROOT__.\DIRECTORY_SEPARATOR.'bin'.\DIRECTORY_SEPARATOR.'composer.phar';
    $composer_cmd = ' update';
    $cmd =  $composer_exe.$composer_cmd;
    $cmd = prepareWindowsCommandLine($cmd,[]);
    push("running composer" . $composer_cmd);

    chdir(__PUBLIC_ROOT__);
    $descriptorspec =  array(
        array("pipe","r"),
        array("pipe","w"),
        array("pipe","w")
    );

     $process = proc_open($cmd, $descriptorspec, $pipes);
}

if (!is_dir(__COMPOSER_DIR__)) {
    // tell php to automatically flush after every output
    // including lines of output produced by shell commands
    disable_ob();
    runComposer();
    composerHeader();

    exit;
}
