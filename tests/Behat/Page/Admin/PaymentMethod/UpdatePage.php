<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPaymentRestrictionPlugin\Behat\Page\Admin\PaymentMethod;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Channel\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function changeZone(string $zoneCode): void
    {
        $this->getElement('zone')->setValue($zoneCode);
    }

    public function getSingleResourceOnPage(string $elementName): array|bool|string
    {
        return $this->getElement($elementName)->getValue();
    }

    public function activateForShippingMethod(int $shipmentMethodId): void
    {
        $Page = $this->getSession()->getPage();
        $shippingMethodElement = $this->getShippingMethodElement($Page, $shipmentMethodId);
        $shippingMethodElement->setValue(true);
    }

    private function getShippingMethodElement(DocumentElement $Page, int $shipmentMethodId): NodeElement
    {
        $shippingMethodElement = $Page->find(
            'css',
            '#sylius_admin_payment_method_shippingMethods_' . $shipmentMethodId,
        );
        assert($shippingMethodElement instanceof NodeElement);

        return $shippingMethodElement;
    }

    public function isActiveForShippingMethod(int $shipmentMethodId): bool
    {
        $Page = $this->getSession()->getPage();
        $shippingMethodElement = $this->getShippingMethodElement($Page, $shipmentMethodId);

        return (bool) $shippingMethodElement->getValue();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(),
            [
                'zone' => '#sylius_admin_payment_method_zone',
            ],
        );
    }
}
