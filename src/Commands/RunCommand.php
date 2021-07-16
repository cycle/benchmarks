<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Commands;

use DirectoryIterator;
use PhpBench\Console\Application;
use PhpBench\PhpBench;
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

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projects = $input->getArgument('projects');

        $output->writeln('<info>Run benchmarks to projects: '.implode(', ', $projects).'</info>');

        foreach ($projects as $project) {
            $project = 'benchmarks' . DIRECTORY_SEPARATOR . $project;

            $projectDir = ROOT . DIRECTORY_SEPARATOR . $project;

            $this->configureProject($projectDir, $output);

            if ($input->getOption('process')) {
                $this->runAsProcess($project, $output);
            } else {
                $this->runAsApplication($project, $output);
            }
        }

        return Command::SUCCESS;
    }

    protected function configure()
    {
        $this->setDescription('Run benchmark');

        $this
            ->addOption('process', 'p', InputOption::VALUE_NONE, 'Use separate process to run benchmarks')
            ->addArgument('projects', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Projects to bench', $this->getProjects());
    }

    protected function configureProject(string $projectDir, OutputInterface $output): void
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

    /**
     * @param string $project
     * @param OutputInterface $output
     */
    protected function runAsProcess(string $project, OutputInterface $output): void
    {
        $process = new Process([
            'vendor/bin/phpbench',
            'run',
            '--working-dir=' . ROOT . '/' . $project,
            '--bootstrap=' . 'bootstrap.php',
            '--report=' . 'aggregate',
            '--config=' . ROOT . '/phpbench.json',
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

    private function runAsApplication(string $project, OutputInterface $output)
    {
        $container = PhpBench::loadContainer(
            $input = new ArrayInput(
                [
                    'run',
                    '--working-dir' => $project,
                    '--bootstrap' => 'bootstrap.php',
                    '--report' => 'aggregate',
                    '--config' => ROOT . '/phpbench.json',
                    'path' => 'tests'
                ]
            )
        );

        $app = $container->get(Application::class);

        $app->setAutoExit(false);

        $app->run($input, $output);
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
}
