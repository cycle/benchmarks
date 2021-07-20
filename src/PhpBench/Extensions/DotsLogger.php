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
    private bool $firstTime = true;

    public function __construct(
        OutputInterface $output,
        VariantFormatter $formatter,
        TimeUnit $timeUnit,
        private bool $showBench = false
    )
    {
        parent::__construct($output, $formatter, $timeUnit);
    }

    public function benchmarkStart(Benchmark $benchmark): void
    {
        if ($this->showBench) {
            // do not output a line break on the first run
            if (false === $this->firstTime) {
                $this->output->writeln('');
            }
            $this->firstTime = false;

            $projectName = trim(preg_replace('!\s+!', ' ', str_replace('\\', ' ', $benchmark->getClass())));

            $projectName = str_replace([
                'Benchmarks', 'Bench'
            ], '', $this->studly($this->snake($projectName)));

            $this->output->writeln(
                'Bench: ' . $projectName
            );
        }
    }

    public function startSuite(RunnerConfig $config, Suite $suite): void
    {

    }

    public function endSuite(Suite $suite): void
    {

    }


    /**
     * Convert a string to snake case.
     *
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public function snake(string $value, string $delimiter = '_')
    {
        $key = $value;

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return $value;
    }

    public function studly(string $value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return $value;
    }
}
