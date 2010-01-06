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

class Id extends Field
{
    /**
     * Constrcutor
     *
     * @param array $parameters
     */
    public function __construct($parameters=array())
    {
        parent::__construct($parameters);

        $this->blank = false;
        $this->default = 0;
        $this->dataType = 'integer';
    }


    /**
     * Determine if this Character Field is valid
     *
     * @return unknown
     */
    public function isValid()
    {
        if (intval($this->value) != $this->value) {
            $this->validationErrors[] = 'Value must be an integer';
            return false;
        }

        if ($this->value < 0) {
            $this->validationErrors[] = 'Value must be a positive integer';
            return false;
        }

        return true;
    }
}