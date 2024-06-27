<?php
/**
 * Command like Metatag writer for video files.
 */

declare(strict_types=1);

use DI\ContainerBuilder;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions(__CMD_CONFIG__.'/container_bindings.php');

return $containerBuilder->build();
