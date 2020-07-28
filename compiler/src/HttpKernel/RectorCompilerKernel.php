<?php

declare(strict_types=1);

namespace Rector\Compiler\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\AutoBindParameter\DependencyInjection\CompilerPass\AutoBindParameterCompilerPass;
use Symplify\ConsoleColorDiff\ConsoleColorDiffBundle;

final class RectorCompilerKernel extends Kernel
{
    public function getCacheDir(): string
    {
        // manually configured, so it can be replaced in phar
        return sys_get_temp_dir() . '/_rector_compiler';
    }

    public function getLogDir(): string
    {
        // manually configured, so it can be replaced in phar
        return sys_get_temp_dir() . '/_rector_compiler_log';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new ConsoleColorDiffBundle()];
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutoBindParameterCompilerPass());
    }
}
