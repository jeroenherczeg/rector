<?php

declare(strict_types=1);

use Rector\Nette\Rector\ClassMethod\TemplateMagicAssignToExplicitVariableArrayRector;
use Rector\NetteCodeQuality\Rector\ArrayDimFetch\ChangeControlArrayAccessToAnnotatedControlVariableRector;
use Rector\NetteCodeQuality\Rector\Assign\MakeGetComponentAssignAnnotatedRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TemplateMagicAssignToExplicitVariableArrayRector::class);

    $services->set(MakeGetComponentAssignAnnotatedRector::class);

    $services->set(ChangeControlArrayAccessToAnnotatedControlVariableRector::class);
};
