<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Commands\RunStrategy;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ProcessStrategy implements StrategyInterface
{
    public function run(string $project, string $config, int $iterations, int $revolutions, OutputInterface $output): void
    {
        $tag = basename($project);

        $process = new Process([
            'vendor/bin/phpbench',
            'run',
            '--working-dir=' . ROOT . '/' . $project,
            '--bootstrap=' . 'bootstrap.php',
            '--report=' . 'aggregate',
            '--iterations=' . $iterations,
            '--revs=' . $revolutions,
            '--config=' . ROOT . DIRECTORY_SEPARATOR . $config,
            '--tag=' . $tag,
            '--store',
            'tests'
        ]);

        $process->start();

        foreach ($process as $type => $data) {
            $output->write(
                $data,
                false,
                OutputInterface::OUTPUT_NORMAL
            );
        }
    }
}
