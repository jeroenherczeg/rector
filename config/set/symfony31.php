<?php

declare(strict_types=1);

use Rector\Core\Rector\Argument\ArgumentDefaultValueReplacerRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ArgumentDefaultValueReplacerRector::class)
        ->arg('$replacesByMethodAndTypes', [
            # replace args - covers https://github.com/symfony/symfony/blob/3.4/UPGRADE-3.1.md#yaml
            'Symfony\Component\Yaml\Yaml' => [
                'parse' => [
                    1 => [
                        [
                            'before' => [false, false, true],
                            'after' => 'Symfony\Component\Yaml\Yaml::PARSE_OBJECT_FOR_MAP',
                        ], [
                            'before' => [false, true],
                            'after' => 'Symfony\Component\Yaml\Yaml::PARSE_OBJECT',
                        ], [
                            'before' => true,
                            'after' => 'Symfony\Component\Yaml\Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE',
                        ], [
                            'before' => false,
                            'after' => 0,
                        ],
                    ],
                ],
                'dump' => [
                    3 => [
                        [
                            'before' => [false, true],
                            'after' => 'Symfony\Component\Yaml\Yaml::DUMP_OBJECT',
                        ], [
                            'before' => true,
                            'after' => 'Symfony\Component\Yaml\Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE',
                        ],
                    ],
                ],
            ],
        ]);
};
