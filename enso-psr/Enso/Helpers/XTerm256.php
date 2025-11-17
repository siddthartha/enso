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
    #[Pure] static public function intToRGBBytes(int $int): array
    {
        $red = ($int >> 16) & 0xFF;
        $green = ($int >> 8) & 0xFF;
        $blue = $int & 0xFF;

        return [$red, $green, $blue];
    }

    static private function isPiped(): bool
    {
        return self::$isPiped !== null
            ? self::$isPiped
            : (self::$isPiped = Runtime::isPiped());
    }

    static private function output(string $output): string
    {
        return self::isPiped() ? $output : "";
    }

    #[Pure] static private function compile(int $rgb, $isBackground = false): string
    {
        [$red, $green, $blue] = self::intToRGBBytes($rgb);
        $code = $isBackground ? "48" : "38";

        return "\033[{$code};2;{$red};{$green};{$blue}m";
    }

    #[Pure] static private function background(int $rgb): string
    {
        return self::compile($rgb, true);
    }

    static public function clear(): string
    {
        return self::output("\033[0m");
    }

    static public function color(int $foregroundRGB, ?int $backgroundRGB = null): string
    {
        return self::output(
            self::compile($foregroundRGB, false)
            . ($backgroundRGB !== null ? self::background($backgroundRGB) : "")
        );
    }

}
