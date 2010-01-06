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
 * Represents query criterion
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Criterion extends \ArrayObject
{
    /**
     * Type-safe addition of criteria
     *
     * @param \Naked\Objects\Criteria $criteria
     */
    public function add(\Naked\Objects\Criteria $criteria)
    {
        $this->append($criteria);
    }
}
