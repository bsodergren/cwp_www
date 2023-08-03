<?php
/**
 * CWP Media tool
 */

/*
 * CWP Media tool.
 */

if (!function_exists('prepareWindowsCommandLine')) {
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
}
if (!function_exists('runComposer')) {
    function runComposer()
    {
        $composer_exe = 'php '.__PROJECT_ROOT__.\DIRECTORY_SEPARATOR.'bin'.\DIRECTORY_SEPARATOR.'composer.phar';
        $composer_cmd = ' update';
        $cmd = $composer_exe.$composer_cmd;
        $cmd = prepareWindowsCommandLine($cmd, []);
        chdir(__PUBLIC_ROOT__);
        $descriptorspec = [
            ['pipe', 'r'],
            ['pipe', 'w'],
            ['pipe', 'w'],
        ];

        $process = proc_open($cmd, $descriptorspec, $pipes);
    }
}
// tell php to automatically flush after every output
// including lines of output produced by shell commands
runComposer();
