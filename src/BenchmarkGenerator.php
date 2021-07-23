<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base;

class BenchmarkGenerator
{
    public function __construct(private string $directory)
    {
    }

    public function generate(
        string $project,
        string $mapper,
        string $benchmark,
        array $bindings,
        string $namespace
    ): Benchmark
    {
        $className = $this->getClassName($benchmark) . $this->getClassName($mapper);
        $filePath = $this->makeBenchmarkPath($project, $className);

        $file = new \Nette\PhpGenerator\PhpFile;
        $file->addComment('This file is auto-generated. ' . date('Y-m-d H:i:s'));
        $file->setStrictTypes();

        $namespace = $file->addNamespace($namespace);
        $class = $namespace->addClass($className);

        $class
            ->setFinal()
            ->setExtends('\\' . $benchmark);

        $method = $class->addMethod('setUp');
        $method->setReturnType('void');
        $method->addParameter('bindings', [])->setType('array');

        $body = '';
        foreach ($bindings as $alias => $resolver) {
            $body .= '$bindings[\\' . $alias . '::class] = \\' . $resolver . '::class;' . PHP_EOL;
        }
        $body .= 'parent::setUp($bindings);' . PHP_EOL;

        $method->setBody($body);

        return new Benchmark($filePath, $file);
    }

    private function makeBenchmarkPath(string $project, string $name): string
    {
        return $this->directory . DIRECTORY_SEPARATOR . 'benchmarks' . DIRECTORY_SEPARATOR . $name . 'Bench.php';
    }

    private function getClassName(string $class): string
    {
        return basename(str_replace('\\', '/', $class));
    }
}
