<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Field;

use Naked\Field;

class ForeignKey extends Field
{
    /**
     * The primary key
     *
     * @var integer
     */
    protected $pk;

    /**
     * The class this fk points to
     *
     * @var string
     */
    protected $class;

    /**
     * Constructor
     *
     * @param array $parameters
     */
    public function __construct($parameters=array())
    {
        parent::__construct($parameters);

        if (!array_key_exists('class', $parameters)) {
            throw new \Exception('Foreign Key fields require a class property');
        }

        $this->dataType = 'integer';
        $this->class = $parameters['class'];
    }

    /**
     * Determine if this Character Field is valid
     *
     * @return unknown
     */
    public function isValid()
    {
        // Perform any common validation
        if (!parent::isValid()) {
            return false;
        }

        // Check if we have a pk for the related object
        if (strlen($this->value) > $this->maxLength) {
            return false;
        }

        return true;
    }

    public function getSpecification()
    {
        $specification = parent::getSpecification();
        $specification['class'] = $this->class;

        return $specification;
    }
}

