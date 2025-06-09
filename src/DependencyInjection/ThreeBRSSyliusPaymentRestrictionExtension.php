<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPaymentRestrictionPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;

class ThreeBRSSyliusPaymentRestrictionExtension extends AbstractResourceExtension
{
	/** @psalm-suppress UnusedVariable */
	public function load(array $configs, ContainerBuilder $container): void
	{
		$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
		$loader->load('services.yml');
	}
}
