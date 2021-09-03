<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Commands;

use Cycle\Benchmarks\Base\Commands\RunStrategy\PhpBenchPackageStrategy;
use Cycle\Benchmarks\Base\Commands\RunStrategy\ProcessStrategy;
use Cycle\Benchmarks\Base\ProjectFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RunCommand extends Command
{
    protected static $defaultName = 'run';
    protected ProjectFinder $projectFinder;

    public function __construct(string $name = null)
    {
        $this->projectFinder = new ProjectFinder(ROOT . DIRECTORY_SEPARATOR . 'benchmarks');
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        @unlink(ROOT . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'phpbench.log');

        $suiteUuid = dechex((int)date('Ymd')) . substr(sha1(date('YmdHis')), 0, -7);
        putenv("SUITE_UUID=$suiteUuid");

        $projects = $input->getArgument('projects');
        $config = $input->getOption('config');
        $iterations = (int)$input->getOption('iterations');
        $revolutions = (int)$input->getOption('revolutions');
        $filter = (array)$input->getOption('filter');
        $groups = (array)$input->getOption('group');

        $output->writeln('<info>Run benchmarks to projects: ' . implode(', ', $projects) . '</info>');

        $strategy = $input->getOption('process')
            ? new ProcessStrategy()
            : new PhpBenchPackageStrategy();

        foreach ($this->projectFinder as $projectName => $projectDir) {
            if (!in_array($projectName, $projects)) {
                continue;
            }

            $project = 'benchmarks' . DIRECTORY_SEPARATOR . $projectName;

            $this->runComposerCommands($projectDir, $output);

            $strategy->run(
                $project,
                $filter,
                $groups,
                $config,
                $iterations,
                $revolutions,
                $output
            );
        }

        $output->writeln('');

        $this->getApplication()
            ->find('report')
            ->run(new ArrayInput([
                '--id' => $suiteUuid,
                'projects' => $projects,
            ]), $output);

        $output->writeln("Use command <info>php bench report $suiteUuid</info> to show this report");

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
        return array_keys(iterator_to_array($this->projectFinder->find()));
    }

    protected function configure(): void
    {
        $this->setDescription('Run benchmark');

        $this
            ->addOption('process', 'p', InputOption::VALUE_NONE, 'Use separate process to run benchmarks')
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Config file', 'phpbench.json')
            ->addOption('group', 'g', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Groups')
            ->addOption('filter', 'f', InputOption::VALUE_OPTIONAL, 'Filter')
            ->addOption('iterations', 'i', InputOption::VALUE_OPTIONAL, 'Number of iterations', 1)
            ->addOption('revolutions', 'r', InputOption::VALUE_OPTIONAL, 'Number of revolutions', 1)
            ->addArgument('projects', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Projects to bench', $this->getProjects());
    }
}
