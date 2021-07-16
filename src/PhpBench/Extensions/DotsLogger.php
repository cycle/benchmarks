<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\PhpBench\Extensions;

use PhpBench\Benchmark\RunnerConfig;
use PhpBench\Model\Benchmark;
use PhpBench\Model\Suite;
use PhpBench\Progress\VariantFormatter;
use PhpBench\Util\TimeUnit;
use Symfony\Component\Console\Output\OutputInterface;

class DotsLogger extends \PhpBench\Progress\Logger\DotsLogger
{
    private bool $showBench;
    private bool $firstTime = true;


    public function __construct(
        OutputInterface $output,
        VariantFormatter $formatter,
        TimeUnit $timeUnit,
        bool $showBench = false
    ) {
        parent::__construct($output, $formatter, $timeUnit);
        $this->showBench = $showBench;
    }

    public function benchmarkStart(Benchmark $benchmark): void
    {
        if ($this->showBench) {
            // do not output a line break on the first run
            if (false === $this->firstTime) {
                $this->output->writeln('');
            }
            $this->firstTime = false;

            $this->output->writeln(
                'Project: ' . trim(preg_replace('!\s+!', ' ', str_replace('\\', ' ', str_replace([
                    'Benchmarks',
                    $benchmark->getName()
                ], '', $benchmark->getClass()))))
            );
        }
    }

    public function startSuite(RunnerConfig $config, Suite $suite): void
    {

    }

    public function endSuite(Suite $suite): void
    {

    }
}
