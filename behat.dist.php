<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\TesterOptions;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use FriendsOfBehat\SuiteSettingsExtension\ServiceContainer\SuiteSettingsExtension;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use FriendsOfBehat\VariadicExtension\ServiceContainer\VariadicExtension;
use Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Kernel;

return (new Config())
    ->import('tests/Behat/Resources/suites.php')
    ->withProfile(
        (new Profile('default'))
            ->withTesterOptions((new TesterOptions())->withErrorReporting(\E_ALL ^ \E_DEPRECATED))
            ->withExtension(new Extension(MinkExtension::class, [
                'base_url' => 'https://127.0.0.1:8080/',
                'default_session' => 'symfony',
                'sessions' => [
                    'symfony' => [
                        'symfony' => null,
                    ],
                ],
                'show_auto' => false, // do not automatically open browser on error
            ]))
            ->withExtension(new Extension(SymfonyExtension::class, [
                'bootstrap' => 'tests/Application/config/bootstrap.php',
                'kernel' => [
                    'class' => Kernel::class,
                    'environment' => 'test',
                ],
            ]))
            ->withExtension(new Extension(VariadicExtension::class))
            ->withExtension(new Extension(SuiteSettingsExtension::class, [
                'paths' => ['features'],
            ])),
    );
