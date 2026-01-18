<?php declare(strict_types=1);

namespace Enso\Helpers;

use JetBrains\PhpStorm\Pure;

if (!defined("STDIN")) define("STDIN", 0);
if (!defined("STDOUT")) define("STDOUT", 1);

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

    public static function output(string $output): string
    {
        return Runtime::isOutputTty() ? $output : "";
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

    static public function color(?int $foregroundRGB = null, ?int $backgroundRGB = null): string
    {
        return self::output(
            ($foregroundRGB ? self::compile($foregroundRGB, false) : "")
            . ($backgroundRGB !== null ? self::background($backgroundRGB) : "")
        );
    }

    static public function label(string $text, ?int $color = null, ?int $bgColor = null): string
    {
        return self::color($color, $bgColor) . $text . self::clear();
    }
}
