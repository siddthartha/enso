<?php declare(strict_types=1);

namespace Enso\Helpers;

use BadMethodCallException;

/**
 * @package    ansicolors
 * @author     Jose Miguel Pérez Ruiz <josemiguel@perezruiz.com>
 * @copyright  2014 Jose Miguel
 * @license    http://opensource.org/licenses/MIT  The MIT License
 */

/**
 * Define STDOUT constant. STDOUT as constant should be automatically defined
 * in CLI mode but is not in the interactive mode of the php executable by
 * using "php -a" in the shell.
 *
 * Despite the huge amount of comments elsewhere, the "stdout" standard output
 * stream is just the file handle of "1". "php://stdout" is just syntactic
 * sugar to open the same, but file handle 1 should be already opened.
 *
 */
if (!defined("STDOUT"))
{
    define("STDOUT", 1);
}

/**
 * Main "ansi" class. Defined in the root namespace to be able to use it as
 * static class. Just imagine it is a namespace...
 *
 * This class is mainly static. An example of usage follows:
 *
 *  <?
 *      // Outputs the ANSI codes for red background but DOES NOT reset it.
 *      // Color remains red until reset.
 *      echo ansi::red();
 *
 *      // Reset whatever color was in effect
 *      echo ansi::reset();
 *
 *      // Outputs the string in blue color, auto resets color after the string.
 *      echo ansi::blue("This is colored blue"):
 *
 *  ?>
 *
 * Normal colors vs. bright colors.
 * --------------------------------
 *
 * By default, color names in lowercase are faint, normal, or NOT bright. To
 * output the bright color version use the name in all UPPER or Camel Case.
 *
 * Examples:     ansi::BLUE(), ansi::Blue() ==> Bright blue
 *
 *
 * Backgrounds
 * -----------
 *
 * Concatenate colors by "_on_" or "_in_" like in "Black_on_white". This
 * outputs text in a foreground black color over a white background (reversed).
 *
 *
 */
class Ansi
{
    /**
     * Main color table. Index the color names to their binary values
     */
    static private array $colors = [
        "black" => 0,
        "red" => 1,
        "green" => 2,
        "yellow" => 3,
        "blue" => 4,
        "magenta" => 5,
        "cyan" => 6,
        "white" => 7
    ];

    /**
     * Same for styles. Most are not yet used, only "bold" for bright
     * foreground colors.
     */
    static private array $styles = [
        "bold" => 1,
        "normal" => 2,
        "underline" => 4,
        "blink" => 5,
        "reverse" => 7,
        "hidden" => 8
    ];

    /**
     * Variable that holds wether we are in a TTY or piped to a stream.
     * If we are piped, no ANSI color codes are returned.
     *
     * Hopefully being static also works as a cache, in order to prevent all
     * those calls to "posix_isatty" everytime we need to output a color.
     */
    static private bool $piped;

    /**
     * Color aliases. Dictionary containing the new configured color aliases.
     */
    static private array $aliases = [];

    /**
     * The color stack. Here we will be updating the color values in a FIFO
     * such that we can restore previous colors.
     */
    static private array $color_stack = [];

    /**
     * Method used for compiling color names and foreground and background
     * combinations to ANSI escape sequences
     *
     * @param string $color The method to call
     * @return string|null          Returns the ANSI codes, or null if not a color
     * @throws BadMethodCallException
     */
    static public function color(string $color): string|null
    {
        // Don't output ANSI codes if not a TTY.
        // Using a static var for caching the posix_isatty call.
        if (static::$piped = !posix_isatty(STDOUT))
        {
            return "";
        }

        // Manage aliases
        if (array_key_exists($color, static::$aliases))
        {
            return self::color(static::$aliases[$color]);
        }

        // Return if this doesn't seem like a color.
        if (($color_code = static::parse_color($color)) === null)
        {
            throw new BadMethodCallException("Color name '" . $color . "' not found.");
        }

        static::$color_stack[] = $color_code;

        return static::code_to_ansi($color_code);
    }

    /**
     * Restore the previous color, or "reset" if none was previously active.
     *
     * @return  string          The ANSI string to restore de color or reset.
     */
    static function restore(): string
    {
        // Don't output ANSI codes if not a TTY.
        // Using a static var for caching the posix_isatty call.
        if (static::$piped = !posix_isatty(STDOUT))
        {
            return "";
        }

        // Ok, what I'm going to do is counter intuitive. The color on the
        // top of the stack is the _current_ color, so in order to restore
        // the previous color, we must remove it from the stack.
        array_pop(static::$color_stack);

        return count(static::$color_stack)
            ? static::code_to_ansi(end(static::$color_stack))
            : static::reset();
    }

    /**
     * Resets the colors and restores the color codes stack.
     *
     * @return  string          The ANSI reset string
     */
    static function reset(): string
    {
        // Don't output ANSI codes if not a TTY.
        // Using a static var for caching the posix_isatty call.
        if (static::$piped = !posix_isatty(STDOUT))
        {
            return "";
        }

        static::$color_stack = [];

        return "\033[0m";
    }

    /**
     * Defines a new named color. Useful to configure new semantic color names
     * for specific combinations. For example:
     *
     *  ansi::define("error", "Red_on_white");
     *
     * This example defines "error" (use like ansi::error(), etc) to be like
     * the call to "ansi::Red_on_white()".
     *
     * Definitions are recursive, that is, aliases can be nested. Example:
     *
     *  ansi::define("error", "Red_on_white");
     *  ansi::define("default", "error");
     *
     * Now, unsing ansi::default() will use "ansi::error", which in turn is
     * "Red_on_white".
     *
     * @param string $name The name of the new color
     * @param string $color The color combination to use
     */
    static function define(string $name, string $color)
    {
        static::$aliases[$name] = $color;
    }

    /**
     * Parses a color string, either a single color or a combination of colors
     * with format [color]_in_[color]
     *
     * @param string $color The color string to parse.
     * @return integer|null     Returns the color code or null if not a color.
     */
    private static function parse_color(string $color): int|null
    {
        if (!str_contains($color, "_"))
        {
            return static::parse_single_color($color);
        }

        if (!preg_match("/^([^_]+)_(?:in|on)_([^_]+)$/i", $color, $m))
        {
            return null;
        }

        [$_, $fore, $back] = $m;
        $fore = static::parse_single_color($fore);
        $back = static::parse_single_color($back);

        if ($fore === null || $back === null)
        {
            return null;
        }

        return (int) ($back * 0x100) + $fore;
    }

    /**
     * Parses a single color, looking into the color array and detecting if
     * the color must be bright.
     *
     * @param string $color The color to parse. Must be a single color.
     * @return integer|null     Returns the color code or null if not a color.
     */
    private static function parse_single_color(string $color): ?int
    {
        $isupper = ctype_upper($color[0]);
        $colower = strtolower($color);

        if (array_key_exists($colower, static::$colors))
        {
            return static::$colors[$colower] + ($isupper ? 0x10 : 0);
        }

        // Not a color...
        return null;
    }

    /**
     * Converts the color code bits into an ANSI representation.
     * Bits 0-3 are foreground color (0x007)
     * Bit  4 is brightness indicator (0x010)
     * Bits 8-11 are background color (0x700)
     *
     * @param integer $code The color code to convert to ANSI string
     * @return string           Returns the ANSI string ready to print
     */
    private static function code_to_ansi(int $code): string
    {
        $codes = [];

        if ($code & 0xF00)
        {
            $codes[] = 40 + (($code & 0xF00) >> 8);
        }

        if ($code & 0x010)
        {
            $codes[] = static::$styles["bold"];
        }

        $codes[] = 30 + ($code & 0xF);

        return "\033[" . implode(";", $codes) . "m";
    }
}
