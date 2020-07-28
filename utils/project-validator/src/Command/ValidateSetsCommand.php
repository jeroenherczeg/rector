<?php

declare(strict_types=1);

namespace Rector\Utils\ProjectValidator\Command;

use Rector\Set\SetProvider;
use Rector\Utils\ProjectValidator\CpuCoreCountResolver;
use Rector\Utils\ProjectValidator\Process\ParallelTaskRunner;
use Rector\Utils\ProjectValidator\ValueObject\SetTask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

/**
 * We'll only check one file for now.
 * This makes sure that all sets are "runnable" but keeps the runtime at a managable level
 */
final class ValidateSetsCommand extends Command
{
    /**
     * @var string[]
     */
    private const EXCLUDED_SETS = [
        // required Kernel class to be set in parameters
        'symfony-code-quality',
    ];

    /**
     * @var int
     */
    private const SLEEP_IN_SECONDS = 1;

    /**
     * @var string
     */
    private const TESTED_FILE = __DIR__ . '/../../../../src/Rector/AbstractRector.php';

    /**
     * @var CpuCoreCountResolver
     */
    private $cpuCoreCountResolver;

    /**
     * @var ParallelTaskRunner
     */
    private $parallelTaskRunner;

    /**
     * @var SetProvider
     */
    private $staticSetProvider;

    public function __construct(
        CpuCoreCountResolver $cpuCoreCountResolver,
        ParallelTaskRunner $parallelTaskRunner,
        SetProvider $staticSetProvider
    ) {
        $this->cpuCoreCountResolver = $cpuCoreCountResolver;
        $this->parallelTaskRunner = $parallelTaskRunner;
        $this->staticSetProvider = $staticSetProvider;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('[CI] Validate each sets by running it on simple file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $setTasks = $this->createSetTasks();
        $cpuCoreCount = $this->cpuCoreCountResolver->resolve();

        $noErrors = $this->parallelTaskRunner->run($setTasks, $cpuCoreCount, self::SLEEP_IN_SECONDS);
        if (! $noErrors) {
            return ShellCode::ERROR;
        }

        return ShellCode::SUCCESS;
    }

    /**
     * @return SetTask[]
     */
    private function createSetTasks(): array
    {
        $setTasks = [];
        foreach ($this->staticSetProvider->provideSetNames() as $setName) {
            if (in_array($setName, self::EXCLUDED_SETS, true)) {
                continue;
            }

            $setTasks[$setName] = new SetTask(self::TESTED_FILE, $setName);
        }

        return $setTasks;
    }
}
