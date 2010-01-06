<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked;

/**
 * An abstract cache
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
abstract class Cache
{
    protected $server;

    /**
     * Constructor
     *
     * @Inject
     * @param Naked\Configuration $configuration
     */
    public function __construct(\Naked\Application\Configuration $configuration)
    {
        $this->server = $configuration->cache_server;
    }

    /**
     * Get a value from the cache
     *
     * @param string $key
     */
    abstract public function get($key);

    /**
     * Put a value in the cache
     *
     * @param string $key
     * @param mixed $value
     * @param integer $timeToLive
     */
    abstract public function put($key, $value, $timeToLive=null);

    /**
     * Clear a value from the cache
     *
     * @param string $key
     */
    abstract public function clear($key);
}
