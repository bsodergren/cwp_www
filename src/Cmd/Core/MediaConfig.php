<?php
/**
 * Command like Metatag writer for video files.
 */

declare(strict_types=1);

// Command like Metatag writer for video files.

namespace CWPCLI\Core;

/**
 * @property ?array  $db
 * @property ?string $environment
 */
class MediaConfig
{
    protected array $config = [];

    public function __construct(array $env)
    {
        $this->config = [
            'environment' => ($env['APP_ENVIRONMENT'] ?? 'development'),
            'db' => [
                'host' => $env['DB_HOST'],
                'user' => $env['DB_USER'],
                'password' => $env['DB_PASS'],
                'dbname' => $env['DB_DATABASE'],
                'driver' => ($env['DB_DRIVER'] ?? 'pdo_mysql'),
            ],
        ];
    }

    public function __get(string $name)
    {
        return $this->config[$name] ?? null;
    }
}
