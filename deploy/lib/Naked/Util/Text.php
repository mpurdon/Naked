<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Util;

/**
 * Utility function for working with text
 *
 * @package Naked
 * @subpackage Util
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Text
{
    const SMART_SPLIT_REG_EX = '#("(?:[^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'(?:[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'|[^\\s]+)#';

    /**
     * Splits a string in a smart way
     *
     * Generator that splits a string by spaces, leaving quoted phrases together.
     * Supports both single and double quotes, and supports escaping quotes with
     * backslashes. In the output, strings will keep their initial and trailing
     * quote marks.
     *
     * @param string $text
     * @return void
     * @author Matthew Purdon
     */
    public static function smartSplit($text)
    {
        $bits = preg_split(self::SMART_SPLIT_REG_EX, $text);

        foreach ($bits as $bit) {
            // do something
        }
    }

    /**
     * Convert a camel case string into an underscored string
     *
     * @param string $text
     * @return string
     */
    public static function camelCaseToUnderscores($string)
    {
        $withUnderscores = preg_replace('#([A-Z])#', '_$1', $string);
        $lowercase = strtolower($withUnderscores);

        return trim($lowercase, '_');
    }

    /**
     * Convert an underscored string into a camel case string
     *
     * @param string $string
     * @return string
     */
    public static function underscoresToCamelCase($string, $capitalizeStart=false)
    {
        $parts = explode('_', $string);

        if ($capitalizeStart) {
            $parts[0] = ucfirst($parts[0]);
        } else {
            $parts[0] = strtolower($parts[0]);
        }

        return implode('', $parts);
    }

    /**
     * Convert a backslashed string into an underscored string
     *
     * @param string $text
     * @return string
     */
    public static function backslashedToUnderscores($string)
    {
        $withUnderscores = str_replace('\\', '_', $string);
        $lowercase = strtolower($withUnderscores);

        return trim($lowercase, '_');
    }

}
