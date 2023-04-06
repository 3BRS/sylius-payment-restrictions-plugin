<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Channel\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function isSingleResourceOnPage(string $elementName);

    public function changeZone(string $zoneCode): void;

    public function activateForShippingMethod(int $shippmentMethodId): void;

    public function isActivateForShippingMethod(int $shippmentMethodId): bool;
}
