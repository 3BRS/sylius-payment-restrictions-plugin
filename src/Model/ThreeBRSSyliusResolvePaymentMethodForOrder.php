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
    public function __construct(private ZoneMatcherInterface $zoneMatcher)
    {
    }

    public function isEligible(PaymentMethodRestrictionInterface $paymentMethod, OrderInterface $order): bool
    {
        $shippingAddress = $order->getShippingAddress();
        assert($shippingAddress instanceof AddressInterface);

        if (!$this->isAllowedForShippingMethod($paymentMethod, $order)) {
            return false;
        }

        if ($paymentMethod->getZone() === null) {
            return true;
        }

        $zones = $this->zoneMatcher->matchAll($shippingAddress);
        foreach ($zones as $zone) {
            assert($zone instanceof ZoneInterface);
            $paymentMethodZone = $paymentMethod->getZone();

            // @phpstan-ignore-next-line
            if ($paymentMethodZone !== null) {
                $paymentMethodZoneCode = $paymentMethodZone->getCode();
                if ($paymentMethodZoneCode === $zone->getCode()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isAllowedForShippingMethod(
        PaymentMethodRestrictionInterface $paymentMethod,
        OrderInterface $order,
    ): bool
    {
        $shipment = $order->getShipments()->last();
        if (!($shipment instanceof Shipment)) {
            return true;
        }

        $shippingMethod = $shipment->getMethod();
        assert($shippingMethod instanceof ShippingMethodInterface);

        foreach ($paymentMethod->getShippingMethods() as $sm) {
            assert($sm instanceof ShippingMethodInterface);
            if ($sm->getId() === $shippingMethod->getId()) {
                return true;
            }
        }

        return false;
    }
}
