<?php
namespace CWP\Core;


use Dotenv\Dotenv;

class EnvLoader
{
    public static function LoadEnv($directory)
    {
        $fp = @fsockopen('tcp://127.0.0.1', 9912, $errno, $errstr, 1);
        if (!$fp) {
            $env_file = '.env';
        } else {
            $env_file = '.env-server';
        }

        return Dotenv::createUnsafeImmutable($directory, $env_file);
    }
}
