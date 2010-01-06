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

class Char extends Field
{
    /**
     * The maximum length for this field
     *
     * @var integer
     */
    protected $maxLength;

    /**
     * Constrcutor
     *
     * @param array $parameters
     */
    public function __construct($parameters=array())
    {
        parent::__construct($parameters);

        if (!array_key_exists('maxLength', $parameters)) {
            throw new \Exception('Character fields require a maxLength property');
        }

        $this->dataType = 'varchar';
        $this->maxLength = $parameters['maxLength'];
    }

    /**
     * Determine if this Character Field is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        // Perform any common validation
        if (!parent::isValid()) {
            return false;
        }

        // Check if we have exceeded our max length
        if (strlen($this->value) > $this->maxLength) {
            return false;
        }

        return true;
    }

    public function getSpecification()
    {
        $specification = parent::getSpecification();
        $specification['maxLength'] = $this->maxLength;

        return $specification;
    }
}
