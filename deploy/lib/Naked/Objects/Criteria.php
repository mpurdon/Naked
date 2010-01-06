<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Objects;

/**
 * Represents query criteria
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Criteria
{
    protected $property;
    protected $comparitor;
    protected $value;

    const PROPERTY_COMPARITOR_SEPARATOR = '_';
    const COMPARITOR_VALUE_SEPARATOR = '=';

    /**
     * Constructor
     *
     * @param string $string
     */
    public function __construct($string)
    {
       $this->parseString($string);
    }

    /**
     * Magic getter
     */
    public function __get($name)
    {
        // @todo Tighten up the magic getter on criteria
        return $this->$name;
    }

    /**
     * Parses a string into the parts that make up a criteria
     *
     * @param string $string
     */
    protected function parseString($string)
    {
        $matches = array();
        preg_match_all($this->getRegEx(), (string) $string, $matches, PREG_SET_ORDER);

        if (count($matches) == 0) {
            throw new \RuntimeException("The criteria you specified '".print_r($string, true)."' does not follow the format property".self::PROPERTY_COMPARITOR_SEPARATOR."comparitor".self::COMPARITOR_VALUE_SEPARATOR."value");
        }

        $this->property = $matches[0]['property'];
        $this->comparitor = $matches[0]['comparitor'];
        $this->value = $matches[0]['value'];
    }

    /**
     * Get the Regular Expression used to smash a criteria string to bits
     *
     * @return string
     */
    protected function getRegEx()
    {
        $regEx = sprintf('#^(?P<property>\w+)%s(?P<comparitor>\w+)%s(?P<value>.*)$#',
                    preg_quote(self::PROPERTY_COMPARITOR_SEPARATOR),
                    preg_quote(self::COMPARITOR_VALUE_SEPARATOR)
        );

        return $regEx;
    }

    /**
     * String representation of this Criteria
     */
    public function __toString()
    {
        return $this->property . ' ' .
               $this->comparitorAsWords() . ' ' .
               $this->value;
    }

    /**
     * String representation of this criteria's comparitor
     *
     * @return string
     */
    protected function comparitorAsWords()
    {
        switch ($this->comparitor) {
            case 'exact':
                return "is exactly equal to";
            case 'iexact':
                return "after ignoring case is exactly equal to";
            case 'contains':
                return "contains the value";
            case 'icontains':
                return "after ignoring case contains the value";
            case 'in':
                return "is one of";
            case 'gt':
                return "is greater than";
            case 'gte':
                return "is greater than or equal to";
            case 'lt':
                return "is less than";
            case 'lte':
                return "is less than or equal to";
            case 'startswith':
                return "begins with";
            case 'istartswith':
                return "after ignoring case begins with";
            case 'endswith':
                return "ends with";
            case 'iendswith':
                return "after ignoring case ends with";
            case 'range':
                return "is between the values";
            case 'year':
                return "has a year equal to";
            case 'month':
                return "has a month equal to";
            case 'day':
                return "has a day equal to";
            case 'dow':
                return "has a day of week equal to";
            default:
                return '';
        }
    }
}
