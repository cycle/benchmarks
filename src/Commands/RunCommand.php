<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Commands;

use PhpBench\PhpBench;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RunCommand extends Command
{
    protected static $defaultName = 'run';

    private const PROJECTS = [
        "benchmarks/v1",
        "benchmarks/v2",
    ];

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = new Process(['composer', 'du',]);
        $process->run();

        foreach (self::PROJECTS as $project) {
            $process = new Process([
                'vendor/bin/phpbench',
                'run',
                '--working-dir=' . ROOT . '/' . $project,
                '--bootstrap=' . 'bootstrap.php',
                '--report='. 'aggregate',
                '--config=' . ROOT . '/phpbench.json',
                'tests/Benchmarks'
            ]);

            $process->start();

            foreach ($process as $type => $data) {
                $output->write($data);
            }
        }

        return Command::SUCCESS;
    }

    protected function configure()
    {
        $this->setDescription('Run benchmark');
    }
}
