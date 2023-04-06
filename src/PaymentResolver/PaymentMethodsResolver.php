<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPaymentRestrictionPlugin\PaymentResolver;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use ThreeBRS\SyliusPaymentRestrictionPlugin\PaymentResolver\Filter\PaymentMethodsFilter;
use Webmozart\Assert\Assert;

class PaymentMethodsResolver implements PaymentMethodsResolverInterface
{
    public function __construct(
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private PaymentMethodsFilter $paymentMethodsFilter,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function getSupportedMethods(BasePaymentInterface $payment): array
    {
        assert($payment instanceof PaymentInterface);
        Assert::true($this->supports($payment), 'This payment method is not support by resolver');

        $order = $payment->getOrder();
        assert($order instanceof OrderInterface);

        $channel = $order->getChannel();
        assert($channel instanceof ChannelInterface);

        $enabledForChannel = $this->paymentMethodRepository->findEnabledForChannel($channel);
        $result = [];

        foreach ($enabledForChannel as $paymentMethod) {
            if ($this->paymentMethodsFilter->isEligible($paymentMethod, $order)) {
                $result[] = $paymentMethod;
            }
        }

        return $result;
    }

    public function supports(BasePaymentInterface $payment): bool
    {
        if (
            !$payment instanceof PaymentInterface
            || $payment->getOrder() === null
        ) {
            return false;
        }

        $order = $payment->getOrder();

        return
            $order instanceof OrderInterface
            && $order->getChannel() !== null;
    }
}
