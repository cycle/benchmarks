<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Commands\RunStrategy;

use PhpBench\Console\Application;
use PhpBench\PhpBench;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class PhpBenchPackageStrategy implements StrategyInterface
{
    public function run(string $project, string $config, int $iterations, int $revolutions, OutputInterface $output): void
    {
        $tag = basename($project);
        $container = PhpBench::loadContainer(
            $input = new ArrayInput(
                [
                    'run',
                    '--working-dir' => $project,
                    '--bootstrap' => 'bootstrap.php',
                    '--report' => 'aggregate',
                    '--iterations' => $iterations,
                    '--revs' => $revolutions,
                    '--config' => ROOT . DIRECTORY_SEPARATOR . $config,
                    // '--tag' => $tag,
                    // '--store',
                    'path' => 'tests'
                ]
            )
        );

        $app = $container->get(Application::class);

        $app->setAutoExit(false);

        $app->run($input, $output);
    }
}
