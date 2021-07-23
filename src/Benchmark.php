<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base;

class Benchmark
{
    public function __construct(private string $filePatch, private \Nette\PhpGenerator\PhpFile $file)
    {
    }

    public function getFilePatch(): string
    {
        return $this->filePatch;
    }

    public function store(bool $override = false): bool
    {
        if (file_exists($this->filePatch) && !$override) {
            return false;
        }

        file_put_contents(
            $this->getFilePatch(),
            (new \Nette\PhpGenerator\PsrPrinter)->printFile($this->file)
        );

        return true;
    }
}
