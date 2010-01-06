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

class Text extends Field
{
    /**
     * Constrcutor
     *
     * @param array $parameters
     */
    public function __construct($parameters=array())
    {
        parent::__construct($parameters);

        $this->dataType = 'text';
    }

    /**
     * Determine if this Text Field is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        // Perform any common validation
        if (!parent::isValid()) {
            return false;
        }

        return true;
    }
}
