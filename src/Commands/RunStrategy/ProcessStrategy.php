<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Commands\RunStrategy;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ProcessStrategy implements StrategyInterface
{
    public function run(string $project, array $filter, array $groups, string $config, int $iterations, int $revolutions, OutputInterface $output): void
    {
        $tag = basename($project);

        $args = [
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
            'benchmarks'
        ];

        if (!empty($filter)) {
            foreach ($filter as $f) {
                $args[] = '--filter=' . $f;
            }
        }

        if (!empty($groups)) {
            foreach ($groups as $group) {
                $args[] = '--group=' . $group;
            }
        }

        $process = new Process($args, env: [
            'SUITE_UUID' => getenv('SUITE_UUID')
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
