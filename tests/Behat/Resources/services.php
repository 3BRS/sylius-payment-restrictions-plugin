<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->defaults()
        ->public();

    $services->set(\Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Page\Admin\PaymentMethod\UpdatePageInterface::class, \Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Page\Admin\PaymentMethod\UpdatePage::class)
        ->private()
        ->args([
            service(\Behat\Mink\Session::class),
            [],
            service('router'),
            'sylius_admin_payment_method_update',
            service(\Sylius\Behat\Service\Helper\AutocompleteHelperInterface::class),
        ]);

    $services->set('sylius_payment_restriction.context.ui.admin.payment_method', \Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Context\Ui\Admin\ManagingPaymentMethodContext::class)
        ->args([service(\Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Page\Admin\PaymentMethod\UpdatePageInterface::class)]);

    $services->set('sylius_payment_restriction.context.setup.checkout_payment_context', \Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Context\Ui\Shop\CheckoutPaymentContext::class)
        ->args([service('sylius.behat.context.ui.shop.checkout.payment')]);

    $services->set('sylius_payment_restriction.context.setup.payment_method', \Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Context\Setup\PaymentMethodContext::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.repository.payment_method'),
            service('sylius.repository.shipping_method'),
            service('sylius.behat.context.setup.zone'),
        ]);
};
