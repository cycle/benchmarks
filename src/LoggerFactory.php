<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    public function __construct(
        private string $logPath,
        private string $timeFormat = "Y-m-d H:i:s"
    )
    {}

    public function create(): LoggerInterface
    {
        $log = new Logger('name');
        $stream = new StreamHandler($this->logPath);

        $formatter = new LineFormatter(null, $this->timeFormat, true, true);
        $formatter->includeStacktraces();

        $stream->setFormatter($formatter);

        $log->pushHandler($stream);

        return $log;
    }
}
