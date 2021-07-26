<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\PhpBench\Extensions;

use Cycle\Benchmarks\Base\LoggerFactory;
use Monolog\Formatter\LineFormatter;
use PhpBench\Benchmark\RunnerConfig;
use PhpBench\Model\Benchmark;
use PhpBench\Model\Suite;
use PhpBench\Progress\VariantFormatter;
use PhpBench\Util\TimeUnit;
use Symfony\Component\Console\Output\OutputInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
        // Injecting suite uuid from runner to generate identical report file names for all projects
        $refl = new \ReflectionClass($suite);
        $property = $refl->getProperty('uuid');
        $property->setAccessible(true);
        $property->setValue($suite, getenv('SUITE_UUID'));

        // Store all errors to phpbench.log
        $log = $this->createLogger();
        $errorStacks = $suite->getErrorStacks();
        foreach ($errorStacks as $errorStack) {
            $stack = [sprintf(
                "%s::%s",
                $errorStack->getVariant()->getSubject()->getBenchmark()->getClass(),
                $errorStack->getVariant()->getSubject()->getName()
            )];

            $error = $errorStack->getErrors()[0] ?? null;

            if ($error) {
                $stack[] = sprintf(
                    "%s %s",
                    $error->getMessage(),
                    $error->getTrace()
                );
            }

            $log->error(implode("\n", $stack) . "\n\n==================== End of error ============================\n");
        }
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
        return ucwords(str_replace(['-', '_'], ' ', $value));
    }

    protected function createLogger(): Logger
    {
        return (new LoggerFactory(
            ROOT . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'phpbench.log'
        ))->create();
    }
}
