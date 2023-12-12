<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutPaymentContext as BaseCheckoutPaymentContext;
use Webmozart\Assert\Assert;

final class CheckoutPaymentContext implements Context
{
    public function __construct(private BaseCheckoutPaymentContext $checkoutPaymentContext)
    {
    }

    /**
     * @Given /^I can not see ("([^"]+)" payment method) in the list of payment methods$/
     */
    public function shippingMethodAllowsPayingWith(string $name): void
    {
        Assert::throws(function () use ($name) {
            $this->checkoutPaymentContext->iSelectPaymentMethod($name);
        }, ElementNotFoundException::class);
    }
}
