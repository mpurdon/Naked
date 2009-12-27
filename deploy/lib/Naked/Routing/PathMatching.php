<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Routing;

/**
 * Interface specifying that objects implementing it are capable of matching paths
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
interface PathMatching
{
    public function matches($path);
}
