<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base;

use Symfony\Component\Console\Output\OutputInterface;

class BenchmarkProjectGenerator
{
    public function __construct(private string $directory, private OutputInterface $output)
    {
    }

    public function generate(string $project, array $config, bool $override = false): ?string
    {
        $projectDir = $this->makeProjectFolder(
            $project,
            $config['boilerplate'] ?? 'default',
            $config['require'] ?? [],
            $config['locked_paths'] ?? [],
            $override
        );

        $generator = new BenchmarkGenerator($projectDir);

        $bindings = $config['bindings'] ?? [];

        foreach ($config['benchmarks'] as $key => $benchmark) {
            if (is_string($key)) {
                $bindings = array_merge($bindings, $benchmark);
                $benchmark = $key;
            }

            $benchmark = $generator->generate($project, $benchmark, $bindings, $config['namespace'] ?? 'Benchmarks');

            if ($benchmark->store($override)) {
                $this->output->writeln('<info>Benchmark ' . $benchmark->getFilePatch() . ' generated</info>');
            }
        }

        return $projectDir;
    }

    private function makeProjectFolder(string $project, string $boilerplate, array $require, array $lockedPaths, bool $override): string
    {
        $projectDir = $this->directory . $project;

        $lockedPaths[] = '';

        $boilerplateDir = ROOT . '/boilerplate/' . $boilerplate;

        if (is_dir($projectDir)) {
            if (!$override) {
                return $projectDir;
            }

            rrmdir($projectDir, $lockedPaths);
        }


        if (!is_dir($boilerplateDir)) {
            throw new \InvalidArgumentException("Boilerplate ${boilerplate} not exist.");
        }

        copyDir($boilerplateDir, $projectDir, $lockedPaths);

        $this->output->writeln("Project {$projectDir} created from {$boilerplateDir}");

        $this->replaceFileVars($projectDir . DIRECTORY_SEPARATOR . 'composer.json', [
            '$projectNamespace' => "CycleORM\\\\Benchmarks\\\\" . ucfirst($project) . '\\\\',
            '$project' => $project,
            '$benchmarksNamespace' => "Benchmarks\\\\",
            '$require' => json_encode($require, JSON_UNESCAPED_SLASHES)
        ]);

        return $projectDir;
    }

    private function replaceFileVars(string $filePath, array $vars): void
    {
        $content = file_get_contents($filePath);

        $content = str_replace(array_keys($vars), array_values($vars), $content);

        file_put_contents($filePath, $content);
    }
}

function copyDir(string $src, string $dst)
{
    $dir = opendir($src);

    if (!is_dir($dst)) {
        @mkdir($dst);
    }

    while (($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copyDir($src . '/' . $file, $dst . '/' . $file);
            } else {
                if (!file_exists($dst . '/' . $file)) {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
    }

    closedir($dir);
}

function rrmdir($dir, array $exclude)
{
    if (is_dir($dir)) {
        $objects = new \DirectoryIterator($dir);

        foreach ($objects as $object) {
            if ($object->isDot() || in_array($object->getBasename(), $exclude)) {
                continue;
            }

            if (is_dir($object->getRealPath()) && !$object->isLink()) {
                if (in_array($object->getFilename(), $exclude)) {
                    return;
                }

                rrmdir($object->getRealPath(), $exclude);
            } else {
                if (in_array($object->getFilename(), $exclude)) {
                    return;
                }

                unlink($object->getRealPath());
            }
        }

        if (in_array($objects->getFilename(), $exclude)) {
            return;
        }

        rmdir($dir);
    }
}
