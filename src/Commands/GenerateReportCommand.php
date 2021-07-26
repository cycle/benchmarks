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
            '--report' => 'cycle'
        ];

        $container = PhpBench::loadContainer(
            new ArrayInput($args)
        );

        $reports = $container->get(ReportManager::class);

        $xml = $container->get(XmlDriver::class);

        $collection = new SuiteCollection();

        foreach ($this->projectFinder as $projectName => $projectDir) {
            $reflection = new ReflectionClass($xml);
            $property = $reflection->getProperty('path');
            $property->setAccessible(true);

            $property->setValue($xml, $projectDir . DIRECTORY_SEPARATOR . '.phpbench' . DIRECTORY_SEPARATOR . 'storage');

            try {
                $reportId = $container->get(UuidResolver::class)->resolve($input->getArgument('id'));
            } catch (\Throwable $e) {
                $output->writeln('<error>Report id not found</error>');
                return Command::INVALID;
            }

            $collection->mergeCollection(
                $xml->fetch($reportId)
            );
        }

        $reports->renderReports($collection, ['grouped', /*'chart'*/], ['console']);

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setDescription('Generate benchmark report');
        $this
            ->addArgument('id', InputArgument::OPTIONAL, 'Report ID', 'latest')
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Config file', 'phpbench.json');
    }
}
