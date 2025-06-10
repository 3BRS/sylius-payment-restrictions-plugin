<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPaymentRestrictionPlugin\Model;

use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Shipping\Model\Shipment;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

class ThreeBRSSyliusResolvePaymentMethodForOrder
{
    public function __construct(private readonly ZoneMatcherInterface $zoneMatcher) {}

    public function isEligible(PaymentMethodRestrictionInterface $paymentMethod, OrderInterface $order): bool
    {
        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress instanceof AddressInterface) {
            return false;
        }

        if (!$this->isAllowedForShippingMethod($paymentMethod, $order)) {
            return false;
        }

        $paymentMethodZone = $paymentMethod->getZone();
        if ($paymentMethodZone === null) {
            return true;
        }

        $zones = $this->zoneMatcher->matchAll($shippingAddress);
        foreach ($zones as $zone) {
            if ($zone instanceof ZoneInterface && $paymentMethodZone->getCode() === $zone->getCode()) {
                return true;
            }
        }

        return false;
    }

    public function isAllowedForShippingMethod(
        PaymentMethodRestrictionInterface $paymentMethod,
        OrderInterface $order,
    ): bool {
        $shipment = $order->getShipments()->last();
        if (!$shipment instanceof Shipment) {
            return true;
        }

        $shippingMethod = $shipment->getMethod();
        if (!$shippingMethod instanceof ShippingMethodInterface) {
            return false;
        }

        foreach ($paymentMethod->getShippingMethods() as $sm) {
            if ($sm instanceof ShippingMethodInterface && $sm->getId() === $shippingMethod->getId()) {
                return true;
            }
        }

        return false;
    }
}
