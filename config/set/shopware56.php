<?php

declare(strict_types=1);

use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    # See https://github.com/shopware/shopware/blob/5.6/UPGRADE-5.6.md
    $containerConfigurator->import(__DIR__ . '/elasticsearch-dsl50.php');

    $services = $containerConfigurator->services();

    $services->set(RenameMethodRector::class)
        ->arg('$oldToNewMethodsByClass', [
            'Enlight_Controller_Response_Response' => [
                'getHttpResponseCode' => 'getStatusCode',
                'setHttpResponseCode' => 'setStatusCode',
                'sendCookies' => 'sendHeaders',
                'setBody' => 'setContent',
            ],
        ]);
};
