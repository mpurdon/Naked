<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked;

use Naked\Annotations\Builder;
use Naked\Annotations\Registry;

/**
 * A class or method annotation
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Annotations extends \ArrayObject
{
    /**
     * @var Naked\Annotations\Builder
     */
    protected $builder;

    /**
     * @var Naked\Annotations\Registry
     */
    protected $registry;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->builder = new Builder();
        $this->registry = new Registry();
    }

    /**
     * Get annotations for the provided class name
     *
     * @param string $class
     * @return Naked\Annotations
     */
    public function forClass($class)
    {
        if ($this->registry->has($class)) {
            return $this->registry->get($class);
        } else {
            $annotations = $this->builder->get($class);
            if ($annotations) {
                $this->registry->set($class, $annotations);
                return $annotations;
            }
        }

        throw new \RuntimeException("Could not get any annotations for class {$class}");
    }
}
