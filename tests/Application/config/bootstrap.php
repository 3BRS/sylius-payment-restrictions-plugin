<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '../../../vendor/autoload.php';

if (($_SERVER['APP_ENV'] ?? '') === 'test') {
    /* to avoid Behat errors like
     * --- Failed scenarios: 8192: Function libxml_disable_entity_loader() is deprecated in vendor/symfony/dom-crawler/Crawler.php line 1183
     * --- constant NumberFormatter::TYPE_CURRENCY is deprecated in /srv/sylius/vendor/twig/intl-extra/IntlExtension.php line 40" at /srv/sylius/vendor/twig/intl-extra/IntlExtension.php line 40
     */
    error_reporting(E_ALL ^ E_DEPRECATED);
}

// Load cached env vars if the .env.local.php file exists
// Run "composer dump-env prod" to create it (requires symfony/flex >=1.2)
if (is_readable(dirname(__DIR__) . '/.env.local.php') && is_array($env = @include dirname(__DIR__) . '/.env.local.php')) {
    $_SERVER += $env;
    $_ENV    += $env;
} elseif (!class_exists(Dotenv::class)) {
    throw new RuntimeException('Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

    return;
} else {
    // load all the .env files
    (new Dotenv())->loadEnv(dirname(__DIR__) . '/.env');
}

$_SERVER['APP_ENV']   = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'dev';
$_SERVER['APP_DEBUG'] ??= $_ENV['APP_DEBUG'] ?? 'prod' !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], \FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
