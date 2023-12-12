<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Channel\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function getSingleResourceOnPage(string $elementName): array|bool|string;

    public function changeZone(string $zoneCode): void;

    public function activateForShippingMethod(int $shipmentMethodId): void;

    public function isActiveForShippingMethod(int $shipmentMethodId): bool;
}
