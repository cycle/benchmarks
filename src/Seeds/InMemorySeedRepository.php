<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Seeds;

class InMemorySeedRepository extends FileSeedRepository
{
    public function __construct(protected array $data)
    {
    }
}
