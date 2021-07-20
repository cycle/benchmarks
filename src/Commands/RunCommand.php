<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Commands;

use Cycle\Benchmarks\Base\Commands\RunStrategy\PhpBenchPackageStrategy;
use Cycle\Benchmarks\Base\Commands\RunStrategy\ProcessStrategy;
use DirectoryIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RunCommand extends Command
{
    protected static $defaultName = 'run';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projects = $input->getArgument('projects');
        $config = $input->getOption('config');
        $iterations = (int)$input->getOption('iterations');
        $revolutions = (int)$input->getOption('revolutions');

        $output->writeln('<info>Run benchmarks to projects: ' . implode(', ', $projects) . '</info>');

        $strategy = $input->getOption('process')
            ? new ProcessStrategy()
            : new PhpBenchPackageStrategy();

        foreach ($projects as $projectName) {
            $project = 'benchmarks' . DIRECTORY_SEPARATOR . $projectName;

            $projectDir = ROOT . DIRECTORY_SEPARATOR . $project;

            $this->runComposerCommands($projectDir, $output);
            $strategy->run($project, $config, $iterations, $revolutions, $output);
        }

        return Command::SUCCESS;
    }

    protected function runComposerCommands(string $projectDir, OutputInterface $output): void
    {
        if (!is_dir($projectDir . DIRECTORY_SEPARATOR . 'vendor')) {
            $command = ['composer', 'install'];
            $message = 'Install composer packages';
        } else {
            $command = ['composer', 'du'];
            $message = null;
        }

        $process = new Process($command, $projectDir);
        $process->run();

        if ($message) {
            $output->writeln("<info>$message</info>");
        }
    }

    protected function getProjects(): array
    {
        $dirs = new DirectoryIterator(ROOT . DIRECTORY_SEPARATOR . 'benchmarks');
        $projects = [];

        foreach ($dirs as $dir) {
            if ($dir->isDot()) {
                continue;
            }
            $projects[] = $dir->getFileName();
        }

        return $projects;
    }

    protected function configure(): void
    {
        $this->setDescription('Run benchmark');

        $this
            ->addOption('process', 'p', InputOption::VALUE_NONE, 'Use separate process to run benchmarks')
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Config file', 'phpbench.json')
            ->addOption('iterations', 'i', InputOption::VALUE_OPTIONAL, 'Number of iterations', 1)
            ->addOption('revolutions', 'r', InputOption::VALUE_OPTIONAL, 'Number of revolutions', 1)
            ->addArgument('projects', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Projects to bench', $this->getProjects());
    }
}
