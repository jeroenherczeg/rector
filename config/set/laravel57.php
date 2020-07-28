<?php

declare(strict_types=1);

use Rector\Core\Rector\Argument\ArgumentAdderRector;
use Rector\Core\Rector\Argument\ArgumentRemoverRector;
use Rector\Core\Rector\Visibility\ChangeMethodVisibilityRector;
use Rector\Laravel\Rector\StaticCall\Redirect301ToPermanentRedirectRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

# see: https://laravel.com/docs/5.7/upgrade

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ChangeMethodVisibilityRector::class)
        ->arg('$methodToVisibilityByClass', [
            'Illuminate\Routing\Router' => [
                'addRoute' => 'public',
            ],
            'Illuminate\Contracts\Auth\Access\Gate' => [
                'raw' => 'public',
            ],
        ]);

    $services->set(ArgumentAdderRector::class)
        ->arg('$positionWithDefaultValueByMethodNamesByClassTypes', [
            'Illuminate\Auth\Middleware\Authenticate' => [
                'authenticate' => [
                    'name' => 'request',
                ],
            ],
            'Illuminate\Foundation\Auth\ResetsPasswords' => [
                'sendResetResponse' => [
                    'name' => 'request',
                    'type' => 'Illuminate\Http\Illuminate\Http',
                ],
            ],
            'Illuminate\Foundation\Auth\SendsPasswordResetEmails' => [
                'sendResetLinkResponse' => [
                    'name' => 'request',
                    'type' => 'Illuminate\Http\Illuminate\Http',
                ],
            ],
        ]);

    $services->set(Redirect301ToPermanentRedirectRector::class);

    $services->set(ArgumentRemoverRector::class)
        ->arg('$positionsByMethodNameByClassType', [
            'Illuminate\Foundation\Application' => [
                'register' => [
                    1 => [
                        'name' => 'options',
                    ],
                ],
            ],
        ]);
};
