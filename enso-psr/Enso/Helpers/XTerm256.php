<?php declare(strict_types=1);

namespace Enso\Helpers;

use JetBrains\PhpStorm\Pure;

if (!defined("STDOUT"))
{
    define("STDOUT", 1);
}

class XTerm256
{
    static private ?bool $isPiped = null;

    /**
     * @param int $int
     * @return array [r,g,b]
     */
    static public function intToRGBBytes(int $int): array
    {
        $red = ($int >> 16) & 0xFF;
        $green = ($int >> 8) & 0xFF;
        $blue = $int & 0xFF;

        return [$red, $green, $blue];
    }

    static public function isPiped(): bool
    {
        return self::$isPiped !== null
            ? self::$isPiped
            : !posix_isatty(STDOUT);
    }

    #[Pure] static public function compile(int $rgb, $isBackground = false): string
    {
        if (self::isPiped())
        {
            return "";
        }

        [$red, $green, $blue] = self::intToRGBBytes($rgb);
        $code = $isBackground ? "48" : "38";

        return "\033[{$code};2;{$red};{$green};{$blue}m";
    }

    #[Pure] static public function color(int $rgb): string
    {
        if (self::isPiped())
        {
            return "";
        }

        return self::compile($rgb, false);
    }

    #[Pure] static public function background(int $rgb): string
    {
        if (self::isPiped())
        {
            return "";
        }

        return self::compile($rgb, true);
    }

    #[Pure] static public function clear(): string
    {
        if (self::isPiped())
        {
            return "";
        }

        return "\033[0m";
    }
}
