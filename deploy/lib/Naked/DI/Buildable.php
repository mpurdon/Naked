<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\DI;

/**
 * Indicates that these objects are buildable by the Naked\DI\Builder
 *
 *  @see Naked\DI\Builder
 *  @author Matthew Purdon <matthew@codenaked.org>
 */
interface Buildable
{
    /**
     * Specify the class type we are handling
     *
     * @param string $class
     */
    public function build($class);

    /**
     * Specify the class we will build for this type
     *
     * @param string $using
     */
    public function using($using);

    /**
     * Specify that when fetching this service, we do so as a singleton
     *
     * By default all junk is singleton
     */
    public function singleton();

    /**
     * Specify what context we are talking about with this specification.
     *
     * Be default we use the context 'default'
     * @param string $context
     */
    public function forContext($context);

    /**
     * Specify a setter value for the object once we instantiate it
     *
     * @param string $property
     * @param mixed $value
     */
    public function having($property, $value);

    /**
     * Determine if this specification is valid
     *
     * @return boolean
     */
    public function isValid();
}
