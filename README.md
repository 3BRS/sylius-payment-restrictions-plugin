<p align="center">
    <a href="https://www.3brs.com" target="_blank">
        <img src="https://3brs1.fra1.cdn.digitaloceanspaces.com/3brs/logo/3BRS-logo-sylius-200.png"/>
    </a>
</p>

<h1 align="center">
    Payment Restrictions Plugin
    <br />
    <a href="https://packagist.org/packages/3brs/sylius-payment-restrictions-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/3brs/sylius-payment-restrictions-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/3brs/sylius-payment-restrictions-plugin" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/3brs/sylius-payment-restrictions-plugin.svg" />
    </a>
    <a href="https://circleci.com/gh/3BRS/sylius-payment-restrictions-plugin" title="Build status" target="_blank">
        <img src="https://circleci.com/gh/3BRS/sylius-payment-restrictions-plugin.svg?style=shield" />
    </a>
</h1>

## Features

-   Restrict payment method by zone. This enables to limit selected payment methods to specific zones or areas from the
    delivery address.
-   Restrict payment method by shipping method - this means that it can disable specific shipment-payment combinations.

<p align="center">
	<img src="https://github.com/3BRS/sylius-payment-restrictions-plugin/blob/sylius_2_upgrade_AK/doc/admin.png?raw=true">
</p>

## Installation

1. Run `$ composer require 3brs/sylius-payment-restrictions-plugin`
1. Add plugin class to your `config/bundles.php`

    ```php
    return [
       ...
       ThreeBRS\SyliusPaymentRestrictionPlugin\ThreeBRSSyliusPaymentRestrictionPlugin::class => ['all' => true],
    ];
    ```

1. Your Entity `PaymentMethod` has to implement `\ThreeBRS\SyliusPaymentRestrictionPlugin\Model\PaymentMethodRestrictionInterface`. You can use
   Trait `ThreeBRS\SyliusPaymentRestrictionPlugin\Model\PaymentMethodRestrictionTrait`

    ```php
        <?php

        declare(strict_types=1);

        namespace Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Entity;

        use Doctrine\Common\Collections\ArrayCollection;
        use Doctrine\ORM\Mapping as ORM;
        use Sylius\Component\Core\Model\PaymentMethod as BasePaymentMethod;
        use ThreeBRS\SyliusPaymentRestrictionPlugin\Model\PaymentMethodRestrictionInterface;
        use ThreeBRS\SyliusPaymentRestrictionPlugin\Model\PaymentMethodRestrictionTrait;

        #[ORM\Entity]
        #[ORM\Table(name: 'sylius_payment_method')]
        class PaymentMethod extends BasePaymentMethod implements PaymentMethodRestrictionInterface
        {
            use PaymentMethodRestrictionTrait;

            public function __construct()
            {
                parent::__construct();

                $this->shippingMethods = new ArrayCollection();
            }
        }

    ```

1. Add to `@SyliusAdmin/PaymentMethod/_form.html.twig` (you will have to probably copy-paste original as there is no way to extend that section)

    ```twig
    {# ... #}

        <div class="mb-4">
            {# ... #}

            <div class="row">
                <div class="col-md-6">
                    {{ form_row(form.zone) }}
                </div>
                <div class="col-md-6">
                    {{ form_row(form.shippingMethods) }}
                </div>
            </div>
        </div>
    {# ... #}
    ```

1. Create and run doctrine database migrations
    ```bash
    bin/console doctrine:migrations:diff
    bin/console doctrine:migrations:migrate
    ```

-   how to use your own entity
    see [Sylius docs - Customizing Models](https://docs.sylius.com/en/1.12/customization/model.html)

## Development

### Usage

-   Alter plugin in `/src`
-   See `bin/` dir for useful commands

### Testing

After your changes you must ensure that the tests are still passing.

```bash
composer install
bin/console doctrine:database:create --if-not-exists --env=test
bin/console doctrine:schema:update --complete --force --env=test
yarn --cwd tests/Application install
yarn --cwd tests/Application build

bin/behat
bin/phpstan.sh
bin/ecs.sh
vendor/bin/phpspec run
```

### Opening Sylius with your plugin

1. Install symfony CLI command: https://symfony.com/download
    - hint: for Docker (with Ubuntu) use _Debian/Ubuntu — APT based
      Linux_ installation steps as `root` user and without `sudo` command
        - you may need to install `curl` first `apt-get update && apt-get install curl --yes`
2. Run app

```bash
(cd tests/Application && APP_ENV=test bin/console sylius:fixtures:load)
(cd tests/Application && APP_ENV=test symfony server:start --dir=public --port=8080)
```

-   change `APP_ENV` to `dev` if you need it

## License

This library is under the MIT license.

## Credits

Developed by [3BRS](https://3brs.com)<br>
Forked from [manGoweb](https://github.com/mangoweb-sylius/SyliusPaymentRestrictionsPlugin).
