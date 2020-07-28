<?php

declare(strict_types=1);

use Rector\Core\Rector\Property\InjectAnnotationClassRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(InjectAnnotationClassRector::class)
        ->arg('$annotationClasses', ['DI\Annotation\Inject']);
};
