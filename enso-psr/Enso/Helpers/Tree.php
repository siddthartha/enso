<?php

namespace Enso\Helpers;

class Tree
{
    const DEPTH_FIRST = 'depth';
    const BREADTH_FIRST = 'breadth';

    public function __construct(
        private array $tree,
        protected string $type = self::DEPTH_FIRST,
    ) {

    }

    public function next(): iterable
    {
        return match($this->type)
        {
            self::DEPTH_FIRST => $this->traverseDepth($this->tree),
            self::BREADTH_FIRST => $this->traverseBreadth($this->tree),
            default => [],
        };
    }

    private function traverseDepth(array &$tree): iterable
    {
        foreach ($tree as $key => $value)
        {
            yield [$key => $value];

            if (is_array($value))
            {
                yield from self::traverseDepth($value);
            }
        }
    }

    private function traverseBreadth(array &$tree): iterable
    {
        $nextLevel = [];

        foreach ($tree as $key => &$value)
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
