<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base;


use ArrayIterator;
use InvalidArgumentException;

class Seeds implements \ArrayAccess, \IteratorAggregate
{
    /**
     * The items contained in the seed.
     *
     * @var array
     */
    protected $items = [];

    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * Get all of the items in the seed.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Execute a callback over each item.
     *
     * @param callable $callback
     * @return $this
     */
    public function each(callable $callback)
    {
        foreach ($this as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * Chunk the seeds into chunks of the given size.
     *
     * @param int $size
     * @return static
     */
    public function chunk(int $size)
    {
        if ($size <= 0) {
            return new static;
        }

        $chunks = [];

        foreach (array_chunk($this->items, $size, true) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }

    public function first(): array
    {
        return array_shift($this->items);
    }

    /**
     * Get one or a specified number of items randomly from the collection.
     *
     * @param int|null $number
     * @return static|mixed
     *
     * @throws InvalidArgumentException
     */
    public function random(int $number = null)
    {
        $requested = is_null($number) ? 1 : $number;
        $count = count($this->items);

        if ($requested > $count) {
            throw new InvalidArgumentException(
                "You requested {$requested} items, but there are only {$count} items available."
            );
        }

        if (is_null($number)) {
            $array = [$this->items[array_rand($this->items)]];
        } else if ($number === 0) {
            $array = [];
        } else {
            $keys = array_rand($this->items, $number);

            $array = [];

            foreach ((array)$keys as $key) {
                $results[] = $this->items[$key];
            }
        }

        return new static($array);
    }

    /**
     * Reverse items order.
     *
     * @return static
     */
    public function reverse()
    {
        return new static(array_reverse($this->items, true));
    }

    /**
     * Shuffle the items in the seed.
     *
     * @param int|null $seed
     * @return static
     */
    public function shuffle(int $seed = null)
    {
        if (is_null($seed)) {
            shuffle($this->items);
        } else {
            mt_srand($seed);
            shuffle($this->items);
            mt_srand();
        }

        return new static($this->items);
    }

    /**
     * Slice the underlying seed array.
     *
     * @param int $offset
     * @param int|null $length
     * @return static
     */
    public function slice(int $offset, int $length = null)
    {
        return new static(array_slice($this->items, $offset, $length, true));
    }

    /**
     * Skip the first {$count} items.
     *
     * @param int $count
     * @return static
     */
    public function skip(int $count)
    {
        return $this->slice($count);
    }

    /**
     * Take the first or last {$limit} items.
     *
     * @param int $limit
     * @return static
     */
    public function take(int $limit)
    {
        if ($limit < 0) {
            return $this->slice($limit, abs($limit));
        }

        return $this->slice(0, $limit);
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Count the number of items in the seed.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param int $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * Get an item at a given offset.
     *
     * @param int $key
     * @return array
     */
    public function offsetGet($key): array
    {
        return $this->items[$key];
    }

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }
}
