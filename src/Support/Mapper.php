<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Support;

final class Mapper
{
    /**
     * @template T
     * @param array $items
     * @param callable(array): T $callback
     * @return T[]
     */
    public static function mapArray(array $items, callable $callback): array
    {
        return array_map($callback, $items);
    }

    /**
     * @template T
     * @param array $data
     * @param callable(array): T $callback
     * @return T
     */
    public static function mapSingle(array $data, callable $callback): mixed
    {
        return $callback($data);
    }
}
