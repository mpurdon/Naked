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

        if (is_array($parameters)) {
            $this->parameters = $parameters;
        } else {
            $this->parameters = explode(' ', $parameters);
        }
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
     * Return the parameters of this annotation
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
