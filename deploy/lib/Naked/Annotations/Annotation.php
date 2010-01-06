<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Annotations;

/**
 * A class or method annotation
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Annotation
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * Constructor
     *
     * @param string $type
     */
    public function __construct($type, $parameters)
    {
        $this->type = $type;

        if (!is_array($parameters)) {
            $parameters = explode(' ', $parameters);
            $parameters = $this->parseParameters($parameters);
        }

        $this->parameters = $parameters;
    }

    /**
     * Processes raw annotation parameters into an hash
     *
     * @param array $parameters
     * @return $array
     */
    protected function parseParameters($parameters)
    {
        $finalParameters = array();

        foreach ($parameters as $parameter) {
            if (strpos($parameter, '=') === false) {
                $finalParameters[$parameter] = true;
                continue;
            }

            list($key, $value) = explode('=', $parameter);
            $finalParameters[$key] = $value;
        }

        return $finalParameters;
    }


    /**
     * Return the type of this annotation
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Determine if this Annotation is a certain type
     *
     * @param string $type
     * @return boolean
     */
    public function isA($type)
    {
        return strcasecmp($this->type,$type) === 0;
    }

    /**
     * Return the parameters of this annotation
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
