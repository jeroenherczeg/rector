<?php

declare(strict_types=1);

use Rector\Sensio\Rector\FrameworkExtraBundle\TemplateAnnotationToThisRenderRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TemplateAnnotationToThisRenderRector::class);
};
