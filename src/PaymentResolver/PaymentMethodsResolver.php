<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPaymentRestrictionPlugin\PaymentResolver;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use ThreeBRS\SyliusPaymentRestrictionPlugin\Model\PaymentMethodRestrictionInterface;
use ThreeBRS\SyliusPaymentRestrictionPlugin\Model\ThreeBRSSyliusResolvePaymentMethodForOrder;
use Webmozart\Assert\Assert;

class PaymentMethodsResolver implements PaymentMethodsResolverInterface
{
    /**
     * @param PaymentMethodRepositoryInterface<PaymentMethodInterface> $paymentMethodRepository
     */
    public function __construct(
        private readonly PaymentMethodRepositoryInterface $paymentMethodRepository,
        private readonly ThreeBRSSyliusResolvePaymentMethodForOrder $paymentOrderResolver,
    ) {
    }

    /**
     * @return array<PaymentMethodInterface|PaymentMethodRestrictionInterface>
     */
    public function getSupportedMethods(BasePaymentInterface $subject): array
    {
        \assert($subject instanceof PaymentInterface);
        Assert::true($this->supports($subject), 'This payment method is not support by resolver');

        $order = $subject->getOrder();
        \assert($order instanceof OrderInterface);

        $channel = $order->getChannel();
        \assert($channel instanceof ChannelInterface);

        $enabledForChannel = $this->paymentMethodRepository->findEnabledForChannel($channel);
        $result = [];
        foreach ($enabledForChannel as $paymentMethod) {
            \assert($paymentMethod instanceof PaymentMethodRestrictionInterface);
            if ($this->paymentOrderResolver->isEligible($paymentMethod, $order)) {
                $result[] = $paymentMethod;
            }
        }

        return $result;
    }

    public function supports(BasePaymentInterface $subject): bool
    {
        if (
            !$subject instanceof PaymentInterface ||
            $subject->getOrder() === null
        ) {
            return false;
        }

        $order = $subject->getOrder();

        return
            $order instanceof OrderInterface &&
            $order->getChannel() !== null;
    }
}
