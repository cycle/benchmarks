<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Commands;

use Cycle\Benchmarks\Base\ProjectFinder;
use PhpBench\Model\SuiteCollection;
use PhpBench\PhpBench;
use PhpBench\Report\ReportManager;
use PhpBench\Storage\Driver\Xml\XmlDriver;
use PhpBench\Storage\UuidResolver;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateReportCommand extends Command
{
    protected static $defaultName = 'report';
    protected ProjectFinder $projectFinder;

    public function __construct(string $name = null)
    {
        $this->projectFinder = new ProjectFinder(ROOT . DIRECTORY_SEPARATOR . 'benchmarks');
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $args = [
            '--config' => ROOT . DIRECTORY_SEPARATOR . $input->getOption('config'),
            '--report' => 'cycle',
        ];


        $container = PhpBench::loadContainer(
            new ArrayInput($args)
        );

        $projects = $input->getArgument('projects');
        $id = $input->getOption('id');

        $reports = $container->get(ReportManager::class);
        $xml = $container->get(XmlDriver::class);
        $collection = new SuiteCollection();

        foreach ($this->projectFinder as $projectName => $projectDir) {
            if (!in_array($projectName, $projects)) {
                continue;
            }

            $reflection = new ReflectionClass($xml);
            $property = $reflection->getProperty('path');
            $property->setAccessible(true);

            $property->setValue($xml, $path = $projectDir . DIRECTORY_SEPARATOR . '.phpbench' . DIRECTORY_SEPARATOR . 'storage');

            try {
                $reportId = $container->get(UuidResolver::class)->resolve($id);
                $collection->mergeCollection(
                    $xml->fetch($reportId)
                );
            } catch (\Throwable $e) {
                $output->writeln("<error>Report id {$id} for project {$projectName} not found</error>");
                continue;
            }
        }

        $reports->renderReports($collection, ['grouped', /*'chart'*/], ['console']);

        return Command::SUCCESS;
    }

    protected function getProjects(): array
    {
        return array_keys(iterator_to_array($this->projectFinder->find()));
    }

    protected function configure(): void
    {
        $this->setDescription('Generate benchmark report');
        $this
            ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'Report ID', 'latest')
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Config file', 'phpbench.json')
            ->addArgument('projects', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Projects to bench', $this->getProjects());
    }
}
