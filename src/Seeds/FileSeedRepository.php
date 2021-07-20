<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Seeds;

class FileSeedRepository implements SeedRepositoryInterface
{
    protected array $data;

    public function __construct(array $data)
    {
        foreach ($data as $class => $file) {
            $this->data[$class] = include_once $file;
        }
    }

    /**
     * Get seed for given entity
     *
     * @param string $entity
     * @return Seeds
     */
    public function get(string $entity): Seeds
    {
        if (!isset($this->data[$entity])) {
            throw new \RuntimeException("Seeds for entity [$entity] not found.");
        }

        return new Seeds($entity, $this->data[$entity]);
    }
}
