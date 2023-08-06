<?php

namespace Enso\Helpers;

class Tree
{
    const DEPTH_FIRST = 'depth';
    const BREADTH_FIRST = 'breadth';

    public static function traverse(array &$tree, string $type = self::DEPTH_FIRST): iterable
    {
        return match($type)
        {
            self::DEPTH_FIRST => self::traverseDepth($tree),
            self::BREADTH_FIRST => self::traverseBreadth($tree),
            default => [],
        };
    }

    private static function traverseDepth(array &$nodes): iterable
    {
        foreach ($nodes as $key => &$value)
        {
            yield $key => $value;

            if (is_array($value))
            {
                yield from self::traverseDepth($value);
            }
        }
    }

    private static function traverseBreadth(array &$nodes): iterable
    {
        $nextLevel = [];

        foreach ($nodes as $key => &$value)
        {
            yield $key => $value;

            if (is_array($value))
            {
                $nextLevel = array_merge($nextLevel, $value);
            }
        }

        if (!empty($nextLevel))
        {
            yield from self::traverseBreadth($nextLevel);
        }
    }
}
