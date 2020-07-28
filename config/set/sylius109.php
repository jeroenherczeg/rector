<?php

declare(strict_types=1);

use Rector\Core\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(AddReturnTypeDeclarationRector::class)
        ->arg('$typehintForMethodByClass', [
            'Sylius\Bundle\CoreBundle\Templating\Helper\VariantResolverHelper' => [
                # source: https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.0.md#upgrade-from-108-to-109
                'resolveVariant' => '?Sylius\Component\Product\Model\ProductVariantInterface',
            ],
        ]);
};
