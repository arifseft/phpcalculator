<?php
// bootstrap.php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__.$_ENV['MODEL_PATH']), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$conn = array(
    'driver' => $_ENV['DB_DRIVER'],
    'path' => __DIR__ . $_ENV['DB_PATH'],
);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);

return $entityManager;
