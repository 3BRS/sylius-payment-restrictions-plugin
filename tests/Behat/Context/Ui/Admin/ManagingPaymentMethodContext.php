<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Page\Admin\PaymentMethod\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingPaymentMethodContext implements Context
{
    public function __construct(
        private UpdatePageInterface $updatePage,
    )
    {
    }

    /**
     * @When /^I select (shipping method "([^"]+)")$/
     */
    public function iSelectShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->updatePage->activateForShippingMethod($shippingMethod->getId());
    }

    /**
     * @When /^(this payment method) is enabled for (shipping method "([^"]+)")$/
     */
    public function thisPaymentMethodHasShippingMethod(
        PaymentMethodInterface $paymentMethod,
        ShippingMethodInterface $shippingMethod,
    ): void
    {
        Assert::true($this->updatePage->isActiveForShippingMethod($shippingMethod->getId()));
    }

    /**
     * @When /^I change (this payment method) zone to (zone "([^"]+)")$/
     */
    public function thisPaymentMethodHasZone(PaymentMethodInterface $paymentMethod, ZoneInterface $zone): void
    {
        $this->updatePage->changeZone((string) $zone->getCode());
    }

    /**
     * @When /^the allowed zone for (this payment method) should be (zone "([^"]+)")$/
     */
    public function thisPaymentMethodZoneShouldBe(PaymentMethodInterface $paymentMethod, ZoneInterface $zone): void
    {
        Assert::eq($this->updatePage->getSingleResourceOnPage('zone'), $zone->getCode());
    }
}
