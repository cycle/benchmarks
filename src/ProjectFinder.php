<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base;

use DirectoryIterator;
use Generator;
use Traversable;

class ProjectFinder implements \IteratorAggregate
{
    public function __construct(private string $directory)
    {
    }

    public function find(): Generator
    {
        $dirs = new DirectoryIterator($this->directory);

        foreach ($dirs as $dir) {
            if ($dir->isDot() || $dir->isFile()) {
                continue;
            }

            yield $dir->getFileName() => $dir->getRealPath();
        }
    }

    public function getIterator(): Traversable
    {
        yield from $this->find();
    }
}
