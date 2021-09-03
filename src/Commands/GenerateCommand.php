<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Commands;

use Cycle\Benchmarks\Base\BenchmarkProjectGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Process\Process;

class GenerateCommand extends Command
{
    protected static $defaultName = 'generate';
    protected bool $override = false;
    private array $projects;

    public function __construct(string $name = null)
    {
        $this->projects = require_once ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'projects.php';

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->override = (bool)$input->getOption('override');

        $projectGenerator = new BenchmarkProjectGenerator(
            $benchmarksDir = ROOT . DIRECTORY_SEPARATOR . 'benchmarks' . DIRECTORY_SEPARATOR,
            $output
        );

        if ($this->override) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('Are you sure?', false, '/^(y)/i');
            if (!$helper->ask($input, $output, $question)) {
                $this->override = false;
            }
        }

        $projects = (array)$input->getArgument('projects');

        $ignore = ['*', '!.gitignore'];
        foreach ($projects as $project) {
            if (!isset($this->projects[$project])) {
                $output->writeln("<error>Project {$project} not found</error>");
                continue;
            }

            $config = $this->projects[$project];

            if (str_starts_with($project, '-')) {
                $project = ltrim($project, '-');
            } else {
                $ignore[] = '!' . $project;
            }

            $dir = $projectGenerator->generate($project, $config, $this->override);

            if ($dir) {
                $this->runComposerCommands($dir, $output);
            }
        }

        file_put_contents($benchmarksDir . '.gitignore', implode(PHP_EOL, $ignore));

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

        if ($message) {
            $output->writeln("$message...");
        }

        $process = new Process($command, $projectDir);
        $process->start();

        foreach ($process as $type => $data) {
            $output->write($data);
        }

        if ($message) {
            $output->writeln('<info>Done</info>');
        }
    }

    protected function configure(): void
    {
        $this->setDescription('Generate benchmarks');

        $this
            ->addOption('override', 'o', InputOption::VALUE_NONE, 'Use separate process to run benchmarks')
            ->addArgument('projects', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Projects to bench', $this->getProjects());
    }

    private function getProjects(): array
    {
        return array_keys($this->projects);
    }
}
