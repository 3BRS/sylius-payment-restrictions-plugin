<?php

declare(strict_types=1);

use Composer\InstalledVersions;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

// DBAL 4 always nests transactions with savepoints
return static function (ContainerConfigurator $container): void {
    if (version_compare((string) InstalledVersions::getVersion('doctrine/dbal'), '4.0.0', '<')) {
        $container->extension('doctrine', ['dbal' => ['use_savepoints' => true]]);
    }
};
