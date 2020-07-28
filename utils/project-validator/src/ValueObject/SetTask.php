<?php

declare(strict_types=1);

namespace Rector\Utils\ProjectValidator\ValueObject;

final class SetTask
{
    /**
     * @var string
     */
    private $pathToFile;

    /**
     * @var string
     */
    private $setName;

    public function __construct(string $pathToFile, string $setName)
    {
        $this->pathToFile = $pathToFile;
        $this->setName = $setName;
    }

    public function getPathToFile(): string
    {
        return $this->pathToFile;
    }

    public function getSetName(): string
    {
        return $this->setName;
    }
}
