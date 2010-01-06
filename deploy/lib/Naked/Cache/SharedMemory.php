<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Cache;

use Naked\Cache;

/**
 * A Memcached cache
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class SharedMemory extends Cache
{
    // @todo Shared memory should be broken out into Zend Cache and APC implementations
    public function get($key)
    {}

    public function put($key, $value, $timeToLive=null)
    {}

    public function clear($key)
    {}
}
