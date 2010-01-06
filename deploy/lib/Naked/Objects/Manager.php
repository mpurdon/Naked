<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Objects;

use Naked\DI;
use Naked\Objects\QuerySet;

/**
 * Manages Domain Object persistance
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Manager
{
    protected $model;
    protected $map;
    protected $persistence;

    /**
     * Constructor
     *
     * @param string $model
     */
    public function __construct($map, $persistence)
    {
        // @todo this should be built using DI
        $this->map = $map;
        $this->persistence = $persistence;
    }

    /**
     * Get a new query set
     *
     * @return QuerySet
     */
    protected function getQuerySet()
    {
        $querySet = new QuerySet($this->map, $this->persistence);

        return $querySet;
    }

    /**
     * Proxy the call through to a new QuerySet
     *
     * We use a whitelist to make sure it's a call that we support
     *
     * @param string $name
     * @param srray $arguments
     */
    public function __call($name, $arguments)
    {
        $whitelist = array('get', 'filter', 'exclude', 'orderby', 'reverse', 'distinct', 'all', 'latest', 'change');

        if (in_array($name, $whitelist)) {
            //echo "Calling $name on new QuerySet with arguments <pre>",print_r($arguments, true),"</pre><br>";
            return call_user_func_array(array($this->getQuerySet(), $name), $arguments);
        }

        throw new \RuntimeException("Tried to call invalid method $name on object manager");
    }

    /**
     * Insert an object into the database
     *
     * @param
     */
    public function insert($object)
    {

    }

    /**
     * Update an object record
     *
     * @param $object
     */
    public function update($object)
    {

    }
}
