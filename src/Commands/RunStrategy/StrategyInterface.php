<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Commands\RunStrategy;

use Symfony\Component\Console\Output\OutputInterface;

interface StrategyInterface
{
    public function run(
        string $project,
        array $filter,
        array $groups,
        string $config,
        int $iterations,
        int $revolutions,
        OutputInterface $output
    ): void;
}
