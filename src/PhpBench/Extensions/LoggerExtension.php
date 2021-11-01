<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\PhpBench\Extensions;

use PhpBench\DependencyInjection\Container;
use PhpBench\DependencyInjection\ExtensionInterface;
use PhpBench\Extension\ConsoleExtension;
use PhpBench\Progress\VariantFormatter;
use PhpBench\Util\TimeUnit;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoggerExtension implements ExtensionInterface
{
    public function load(Container $container): void
    {
        $container->register(ProgressBarLogger::class, function (Container $container) {
            return new ProgressBarLogger(
                $container->get(ConsoleExtension::SERVICE_OUTPUT_ERR),
                $container->get(VariantFormatter::class),
                new TimeUnit(
                    TimeUnit::MILLISECONDS,
                    TimeUnit::MILLISECONDS,
                    TimeUnit::AUTO,
                    1
                ),
                true
            );
        }, ['runner.progress_logger' => ['name' => 'cycle']]);
    }

    public function configure(OptionsResolver $resolver): void
    {
        if (!defined('ROOT')) {
            define('ROOT', __DIR__ . '/../../..');
        }
    }
}
