<?php

declare(strict_types=1);

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Phalcon\Helper;

use RuntimeException;

/**
 * Class Str
 *
 * This class offers quick string functions throughout the framework
 *
 * @package Phalcon\Helper
 */
class Str
{
    public const RANDOM_ALNUM = 0; // Only alpha numeric characters [a-zA-Z0-9]
    public const RANDOM_ALPHA = 1; // Only alphabetical characters [azAZ]
    public const RANDOM_DISTINCT = 5; // Only alpha numeric uppercase characters exclude similar sharacters [2345679ACDEFHJKLMNPRSTUVWXYZ]
    public const RANDOM_HEXDEC = 2; // Only hexadecimal characters [0-9a-f]
    public const RANDOM_NOZERO = 4; // Only numbers without 0 [1-9]
    public const RANDOM_NUMERIC = 3; // Only numbers [0-9]

    /**
     * Converts strings to camelize style
     *
     * ```php
     * use Phalcon\Helper\Str;
     *
     * echo Str::camelize("coco_bongo");            // CocoBongo
     * echo Str::camelize("co_co-bon_go", "-");     // Co_coBon_go
     * echo Str::camelize("co_co-bon_go", "_-");    // CoCoBonGo
     * ```
     *
     * @param string $text
     * @param string|null $delimiter
     * @return string
     * @throws Exception
     */
    final public static function camelize(string $text, $delimiter = null): string
    {
        $preDel = 1;
        $camielizeStr = '';
        $len = strlen($text);

        if (null === $delimiter) {
            $delimiter = "_-";
        } elseif (is_string($delimiter) && $delimiter === '') {
            throw new Exception(
                "The second argument passed to the camelize() must be a string containing at least one character"
            );
        }

        for ($i = 0; $i < $len; $i++) {
            $ch = $text[$i];

            if (strpos($delimiter, $ch) !== false) {
                $preDel = 1;
                continue;
            }

            if ($preDel === 1) {
                $camielizeStr .= strtoupper($ch);
                $preDel = 0;
            } else {
                $camielizeStr .= strtolower($ch);
            }
        }

        return $camielizeStr;
    }

    /**
     * Concatenates strings using the separator only once without duplication in
     * places concatenation
     *
     * ```php
     * $str = Phalcon\Helper\Str::concat(
     *     "/",
     *     "/tmp/",
     *     "/folder_1/",
     *     "/folder_2",
     *     "folder_3/"
     * );
     *
     * echo $str;   // /tmp/folder_1/folder_2/folder_3/
     * ```
     *
     * @param string separator
     * @param string a
     * @param string b
     * @param string ...N
     * @return string
     * @throws Exception
     */
    final public static function concat(): string
    {
        $arguments = func_get_args();

        if (count($arguments) < 3) {
            throw new Exception("concat needs at least three parameters");
        }

        $delimiter = Arr::first($arguments);
        $arguments = Arr::sliceRight($arguments);
        $first = Arr::first($arguments);
        $last = Arr::last($arguments);
        $prefix = "";
        $suffix = "";
        $data = [];

        if (self::startsWith($first, $delimiter)) {
            $prefix = $delimiter;
        }

        if (self::endsWith($last, $delimiter)) {
            $suffix = $delimiter;
        }

        foreach ($arguments as $argument) {
            $data[] = trim($argument, $delimiter);
        }

        return $prefix . implode($delimiter, $data) . $suffix;
    }

    /**
     * Returns number of vowels in provided string. Uses a regular expression
     * to count the number of vowels (A, E, I, O, U) in a string.
     *
     * @param string $text
     * @return int
     */
    final public static function countVowels(string $text): int
    {
        $matches = null;

        preg_match_all("/[aeiou]/i", $text, $matches);

        return count($matches[0]);
    }

    /**
     * Decapitalizes the first letter of the string and then adds it with rest
     * of the string. Omit the upperRest parameter to keep the rest of the
     * string intact, or set it to true to convert to uppercase.
     *
     * @param string $text
     * @param bool $upperRest
     * @param string $encoding
     * @return string
     */
    final public static function decapitalize(string $text, bool $upperRest = false, string $encoding = "UTF-8"): string
    {
        if (function_exists("mb_substr")) {
            $substr = mb_substr($text, 1);
        } else {
            $substr = substr($text, 1);
        }

        if ($upperRest) {
            if (function_exists("mb_strtoupper")) {
                $suffix = mb_strtoupper($substr, $encoding);
            } else {
                $suffix = strtoupper($substr);
            }
        } else {
            $suffix = $substr;
        }

        if (function_exists("mb_strtolower")) {
            return mb_strtolower(mb_substr($text, 0, 1), $encoding) . $suffix;
        }

        return strtolower($text[0]) . $suffix;
    }

    /**
     * Removes a number from a string or decrements that number if it already is defined.
     * defined
     *
     * ```php
     * use Phalcon\Helper\Str;
     *
     * echo Str::decrement("a_1");    // "a"
     * echo Str::decrement("a_2");  // "a_1"
     * ```
     *
     * @param string $text
     * @param string $separator
     * @return string
     */
    final public static function decrement(string $text, string $separator = "_"): string
    {
        $parts = explode($separator, $text);
        $number = $parts[1];

        if (isset($number)) {
            $number--;

            if ($number <= 0) {
                return $parts[0];
            }
        }

        return $parts[0] . $separator . $number;
    }

    /**
     * Accepts a file name (without extension) and returns a calculated
     * directory structure with the filename in the end
     *
     * ```php
     * use Phalcon\Helper\Str;
     *
     * echo Str::dirFromFile("file1234.jpg"); // fi/le/12/
     * ```
     *
     * @param string $file
     * @return string
     */
    final public static function dirFromFile(string $file): string
    {
        $name = pathinfo($file, PATHINFO_FILENAME);
        $start = substr($name, 0, -2);

        if (!empty($start)) {
            $start = str_replace(".", "-", $start);
        }

        if (!$start) {
            $start = $name[0];
        }

        return implode("/", str_split($start, 2)) . "/";
    }

    /**
     * Accepts a directory name and ensures that it ends with
     * DIRECTORY_SEPARATOR
     *
     * ```php
     * use Phalcon\Helper\Str;
     *
     * echo Str::dirSeparator("/home/phalcon"); // /home/phalcon/
     * ```
     *
     * @param string $directory
     * @return string
     */
    final public static function dirSeparator(string $directory): string
    {
        return rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Generates random text in accordance with the template
     *
     * ```php
     * use Phalcon\Helper\Str;
     *
     * // Hi my name is a Bob
     * echo Str::dynamic("{Hi|Hello}, my name is a {Bob|Mark|Jon}!");
     *
     * // Hi my name is a Jon
     * echo Str::dynamic("{Hi|Hello}, my name is a {Bob|Mark|Jon}!");
     *
     * // Hello my name is a Bob
     * echo Str::dynamic("{Hi|Hello}, my name is a {Bob|Mark|Jon}!");
     *
     * // Hello my name is a Zyxep
     * echo Str::dynamic(
     *     "[Hi/Hello], my name is a [Zyxep/Mark]!",
     *     "[", "]",
     *     "/"
     * );
     * ```
     *
     * @param string $text
     * @param string $leftDelimiter
     * @param string $rightDelimiter
     * @param string $separator
     * @return string
     */
    final public static function dynamic(string $text, string $leftDelimiter = "{", string $rightDelimiter = "}", string $separator = "|"): string
    {
        if (substr_count($text, $leftDelimiter) !== substr_count($text, $rightDelimiter)) {
            throw new RuntimeException(
                "Syntax error in string \"" . $text . "\""
            );
        }

        $ldS = preg_quote($leftDelimiter, null);
        $rdS = preg_quote($rightDelimiter, null);
        $pattern = "/" . $ldS . "([^" . $ldS . $rdS . "]+)" . $rdS . "/";
        $matches = [];

        if (!preg_match_all($pattern, $text, $matches, 2)) {
            return $text;
        }

        if (is_array($matches)) {
            foreach ($matches as $match) {
                if (!isset($match[0], $match[1])) {
                    continue;
                }

                $words = explode($separator, $match[1]);
                $word = $words[array_rand($words)];
                $sub = preg_quote($match[0], $separator);
                $text = preg_replace("/" . $sub . "/", $word, $text, 1);
            }
        }

        return $text;
    }

    /**
     * Check if a string ends with a given string
     *
     * ```php
     * use Phalcon\Helper\Str;
     *
     * echo Str::endsWith("Hello", "llo");          // true
     * echo Str::endsWith("Hello", "LLO", false);   // false
     * echo Str::endsWith("Hello", "LLO");          // true
     * ```
     *
     * @param string $text
     * @param string $end
     * @param bool $ignoreCase
     * @return bool
     */
    final public static function endsWith(string $text, string $end, bool $ignoreCase = true): bool
    {
        if ($ignoreCase) {
            return strcmp(substr($text, strlen($text) - strlen($end)), $end) === 0;
        }

        return strcasecmp(substr($text, strlen($text) - strlen($end)), $end) === 0;
    }

    /**
     * Returns the first string there is between the strings from the
     * parameter start and end.
     *
     * @param string $text
     * @param string $start
     * @param string $end
     * @return string
     */
    final public static function firstBetween(string $text, string $start, string $end): string
    {
        if (function_exists('mb_strstr')) {
            $text = (string)mb_strstr(mb_strstr($text, $start), $end, true);
        } else {
            $text = (string)strstr(strstr($text, $start), $end, true);
        }

        return trim(
            $text,
            $start . $end
        );
    }

    /**
     * Makes an underscored or dashed phrase human-readable
     *
     * ```php
     * use Phalcon\Helper\Str;
     *
     * echo Str::humanize("start-a-horse"); // "start a horse"
     * echo Str::humanize("five_cats");     // "five cats"
     * ```
     *
     * @param string $text
     * @return string
     */
    final public static function humanize(string $text): string
    {
        return preg_replace('#[_-]+#', ' ', trim($text));
    }

    /**
     * Lets you determine whether or not a string includes another string.
     *
     * @param string $needle
     * @param string $haystack
     * @return bool
     */
    final public static function includes(string $needle, string $haystack): bool
    {
        if (function_exists("mb_strpos")) {
            return false !== mb_strpos($haystack, $needle);
        }

        return false !== strpos($haystack, $needle);
    }

    /**
     *  Adds a number to a string or increment that number if it already is
     * defined
     *
     * ```php
     * use Phalcon\Helper\Str;
     *
     * echo Str::increment("a");    // "a_1"
     * echo Str::increment("a_1");  // "a_2"
     * ```
     *
     * @param string $text
     * @param string $separator
     * @return string
     */
    final public static function increment(string $text, string $separator = "_"): string
    {
        $parts = explode($separator, $text);
        $number = $parts[1];

        if (isset($number)) {
            $number++;
        } else {
            $number = 1;
        }

        return $parts[0] . $separator . $number;
    }

    /**
     * Compare two strings and returns true if both strings are anagram,
     * false otherwise.
     *
     * @param string $first
     * @param string $second
     * @return bool
     */
    final public static function isAnagram(string $first, string $second): bool
    {
        return count_chars($first, 1) === count_chars($second, 1);
    }

    /**
     * Returns true if the given string is lower case, false otherwise.
     *
     * @param string $text
     * @param string $encoding
     * @return bool
     */
    final public static function isLower(string $text, string $encoding = "UTF-8"): bool
    {
        if (function_exists("mb_strtolower")) {
            return $text === mb_strtolower($text, $encoding);
        }

        return $text === strtolower($text);
    }

    /**
     * Returns true if the given string is a palindrome, false otherwise.
     *
     * @param string $text
     * @return bool
     */
    final public static function isPalindrome(string $text): bool
    {
        return strrev($text) === $text;
    }

    /**
     * Returns true if the given string is upper case, false otherwise.
     *
     * @param string $text
     * @param string $encoding
     * @return bool
     */
    final public static function isUpper(string $text, string $encoding = "UTF-8"): bool
    {
        if (function_exists("mb_strtoupper")) {
            return $text === mb_strtoupper($text, $encoding);
        }

        return $text === strtoupper($text);
    }

    /**
     * Lowercases a string, this function makes use of the mbstring extension if
     * available
     *
     * ```php
     * echo Phalcon\Helper\Str::lower("HELLO"); // hello
     * ```
     *
     * @param string $text
     * @param string $encoding
     * @return string
     */
    final public static function lower(string $text, string $encoding = "UTF-8"): string
    {
        /**
         * 'lower' checks for the mbstring extension to make a correct lowercase
         * transformation
         */
        if (function_exists("mb_strtolower")) {
            return mb_strtolower($text, $encoding);
        }

        return strtolower($text);
    }

    /**
     * Generates a random string based on the given type. Type is one of the
     * RANDOM_* constants
     *
     * ```php
     * use Phalcon\Helper\Str;
     *
     * echo Str::random(Str::RANDOM_ALNUM); // "aloiwkqz"
     * ```
     *
     * @param int $type
     * @param int $length
     * @return string
     */
    final public static function random(int $type = 0, $length = 8): string
    {
        $text = "";

        switch ($type) {
            case self::RANDOM_ALPHA:
                $pool = array_merge(range("a", "z"), range("A", "Z"));
                break;

            case self::RANDOM_HEXDEC:
                $pool = array_merge(range(0, 9), range("a", "f"));
                break;

            case self::RANDOM_NUMERIC:
                $pool = range(0, 9);
                break;

            case self::RANDOM_NOZERO:
                $pool = range(1, 9);
                break;

            case self::RANDOM_DISTINCT:
                $pool = str_split("2345679ACDEFHJKLMNPRSTUVWXYZ");
                break;

            default:
                // RANDOM_ALNUM
                $pool = array_merge(
                    range(0, 9),
                    range("a", "z"),
                    range("A", "Z")
                );

                break;
        }

        $end = count($pool) - 1;

        while (strlen($text) < $length) {
            /** @noinspection RandomApiMigrationInspection */
            $text .= $pool[mt_rand(0, $end)];
        }

        return $text;
    }

    /**
     * Reduces multiple slashes in a string to single slashes
     *
     * ```php
     * // foo/bar/baz
     * echo Phalcon\Helper\Str::reduceSlashes("foo//bar/baz");
     *
     * // http://foo.bar/baz/buz
     * echo Phalcon\Helper\Str::reduceSlashes("http://foo.bar///baz/buz");
     * ```
     *
     * @param string $text
     * @return string
     */
    final public static function reduceSlashes(string $text): string
    {
        return preg_replace("#(?<!:)//+#", "/", $text);
    }

    /**
     * Check if a string starts with a given string
     *
     * ```php
     * use Phalcon\Helper\Str;
     *
     * echo Str::startsWith("Hello", "He");         // true
     * echo Str::startsWith("Hello", "he", false);  // false
     * echo Str::startsWith("Hello", "he");         // true
     * ```
     *
     * @param string $text
     * @param string $start
     * @param bool $ignoreCase
     * @return bool
     */
    final public static function startsWith(string $text, string $start, bool $ignoreCase = true): bool
    {
        if ($ignoreCase) {
            return strcmp(substr($text, 0, strlen($start)), $start) === 0;
        }

        return strcasecmp(substr($text, 0, strlen($start)), $start) === 0;
    }

    /**
     * Uncamelize strings which are camelized
     *
     * ```php
     * use Phalcon\Helper\Str;
     *
     * echo Str::uncamelize("CocoBongo");       // coco_bongo
     * echo Str::uncamelize("CocoBongo", "-");  // coco-bongo
     * ```
     *
     * @param string $text
     * @param null $delimiter
     * @return string
     * @throws Exception
     */
    final public static function uncamelize(string $text, $delimiter = null): string
    {
        $unCamielizeStr = '';
        $len = strlen($text);

        if (null === $delimiter) {
            $delimiter = "_";
        } elseif (is_string($delimiter) && strlen($delimiter) !== 1) {
            throw new Exception(
                "The second argument passed to the uncamelize() must be a string of one character"
            );
        }

        for ($i = 0; $i < $len; $i++) {
            $ch = $text[$i];

            if (ord($ch) === 0) {
                break;
            }

            if (self::isUpper($ch)) {
                if ($i > 0) {
                    $unCamielizeStr .= $delimiter;
                }
                $unCamielizeStr .= self::lower($ch);
            } else {
                $unCamielizeStr .= $ch;
            }
        }

        return $unCamielizeStr;
    }

    /**
     * Makes a phrase underscored instead of spaced
     *
     * ```php
     * use Phalcon\Helper\Str;
     *
     * echo Str::underscore("look behind");     // "look_behind"
     * echo Str::underscore("Awesome Phalcon"); // "Awesome_Phalcon"
     * ```
     *
     * @param string $text
     * @return string
     */
    final public static function underscore(string $text): string
    {
        return preg_replace("#\s+#", "_", trim($text));
    }

    /**
     * Uppercases a string, this function makes use of the mbstring extension if
     * available
     *
     * ```php
     * echo Phalcon\Helper\Str::upper("hello"); // HELLO
     * ```
     *
     * @param string $text
     * @param string $encoding
     * @return string
     */
    final public static function upper(string $text, string $encoding = "UTF-8"): string
    {
        /**
         * 'upper' checks for the mbstring extension to make a correct lowercase
         * transformation
         */
        if (function_exists("mb_strtoupper")) {
            return mb_strtoupper($text, $encoding);
        }

        return strtoupper($text);
    }
}
