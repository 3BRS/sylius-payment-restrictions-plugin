<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use ThreeBRS\SyliusPaymentRestrictionPlugin\Model\PaymentMethodRestrictionInterface;

final class PaymentMethodContext implements Context
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PaymentMethodRepositoryInterface $paymentMethodRepository,
        private readonly ShippingMethodRepositoryInterface $shippingMethodRepository,
    ) {
    }

    /**
     * @Given /^(this payment method) has (zone "([^"]+)")$/
     */
    public function thisPaymentMethodHasZone(PaymentMethodInterface $paymentMethod, ZoneInterface $zone): void
    {
        assert($paymentMethod instanceof PaymentMethodRestrictionInterface);
        $paymentMethod->setZone($zone);
        $this->entityManager->flush();
    }

    /**
     * @Given /^(this payment method) is valid for (shipping method "([^"]+)")$/
     */
    public function thisPaymentMethodIsValidForShippingMethod(
        PaymentMethodInterface $paymentMethod,
        ShippingMethodInterface $shippingMethod,
    ): void {
        assert($paymentMethod instanceof PaymentMethodRestrictionInterface);
        $paymentMethod->getShippingMethods()->add($shippingMethod);
        $this->entityManager->flush();
    }

    /**
     * @Given all payment methods are valid for all shipping methods
     */
    public function allPaymentMethodsAreValidForAllShippingMethods(): void
    {
        /** @var PaymentMethodRestrictionInterface[] $paymentMethods */
        $paymentMethods = $this->paymentMethodRepository->findAll();
        /** @var ShippingMethodInterface[] $shippingMethods */
        $shippingMethods = $this->shippingMethodRepository->findAll();

        foreach ($paymentMethods as $paymentMethod) {
            foreach ($shippingMethods as $shippingMethod) {
                $paymentMethod->getShippingMethods()->add($shippingMethod);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @Given /^("([^"]+)" shipping method) allows paying with ("([^"]+)" payment method)$/
     */
    public function shippingMethodAllowsPayingWith(
        ShippingMethodInterface $shippingMethod,
        PaymentMethodInterface $paymentMethod,
    ): never {
        throw new \Exception();
    }
}
