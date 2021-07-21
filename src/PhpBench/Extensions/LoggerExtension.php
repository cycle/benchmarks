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
        $container->register(DotsLogger::class . '.show', function (Container $container) {
            return new DotsLogger(
                $container->get(ConsoleExtension::SERVICE_OUTPUT_ERR),
                $container->get(VariantFormatter::class),
                $container->get(TimeUnit::class),
                true
            );
        }, ['runner.progress_logger' => ['name' => 'cycle']]);
    }

    public function configure(OptionsResolver $resolver): void
    {
        if (!defined('ROOT')) {
            define('ROOT', __DIR__ . '/../..');
        }
    }
}
