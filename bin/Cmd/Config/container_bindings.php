<?php
/**
 * Command like Metatag writer for video files.
 */

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use CWPCLI\Core\MediaConfig;

use function DI\create;

return [
    Config::class => create(Config::class)->constructor($_ENV),
    EntityManager::class => fn (MediaConfig $config) => EntityManager::create($config->db, 
    ORMSetup::createAttributeMetadataConfiguration([__PROJECT_ROOT__.'/app/Entity'])),
];
