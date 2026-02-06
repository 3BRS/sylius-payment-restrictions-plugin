<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPaymentRestrictionPlugin\Fixture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use ThreeBRS\SyliusPaymentRestrictionPlugin\Model\PaymentMethodRestrictionInterface;

/** @psalm-suppress MissingTemplateParam */
final class PaymentMethodRestrictionFixture extends AbstractFixture
{
    /** @param PaymentMethodRepositoryInterface<\Sylius\Component\Payment\Model\PaymentMethodInterface> $paymentMethodRepository */
    public function __construct(
        private readonly PaymentMethodRepositoryInterface $paymentMethodRepository,
        private readonly RepositoryInterface $zoneRepository,
        private readonly RepositoryInterface $shippingMethodRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /** @param array{custom: array<array{payment_method: string, zone: string|null, shipping_methods: array<string>}>} $options */
    public function load(array $options): void
    {
        foreach ($options['custom'] as $restrictionOptions) {
            $paymentMethod = $this->paymentMethodRepository->findOneBy([
                'code' => $restrictionOptions['payment_method'],
            ]);

            if ($paymentMethod === null) {
                throw new \InvalidArgumentException(sprintf(
                    'Payment method with code "%s" not found.',
                    $restrictionOptions['payment_method'],
                ));
            }

            if (!$paymentMethod instanceof PaymentMethodRestrictionInterface) {
                throw new \InvalidArgumentException(sprintf(
                    'Payment method "%s" does not implement PaymentMethodRestrictionInterface.',
                    $restrictionOptions['payment_method'],
                ));
            }

            if ($restrictionOptions['zone'] !== null) {
                $zone = $this->zoneRepository->findOneBy(['code' => $restrictionOptions['zone']]);
                if ($zone === null) {
                    throw new \InvalidArgumentException(sprintf(
                        'Zone with code "%s" not found.',
                        $restrictionOptions['zone'],
                    ));
                }
                \assert($zone instanceof ZoneInterface);
                $paymentMethod->setZone($zone);
            }

            if (count($restrictionOptions['shipping_methods']) > 0) {
                $shippingMethods = [];
                foreach ($restrictionOptions['shipping_methods'] as $code) {
                    $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $code]);
                    if ($shippingMethod === null) {
                        throw new \InvalidArgumentException(sprintf(
                            'Shipping method with code "%s" not found.',
                            $code,
                        ));
                    }
                    \assert($shippingMethod instanceof ShippingMethodInterface);
                    $shippingMethods[] = $shippingMethod;
                }
                /** @var ArrayCollection<int, ShippingMethodInterface> $collection */
                $collection = new ArrayCollection($shippingMethods);
                $paymentMethod->setShippingMethods($collection);
            }
        }

        $this->entityManager->flush();
    }

    public function getName(): string
    {
        return 'payment_method_restriction';
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->arrayNode('custom')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('payment_method')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('zone')->defaultNull()->end()
                            ->arrayNode('shipping_methods')
                                ->scalarPrototype()->end()
                                ->defaultValue([])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
