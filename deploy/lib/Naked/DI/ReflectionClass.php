<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\DI;

use Naked\Annotations\ReflectionClass as AnnotationReflectionClass;

/**
 * Reflection Class with methods specialized for Dependency Injection
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class ReflectionClass extends AnnotationReflectionClass
{
    /**
     * Determine if this class requires constructor injection
     *
     * @return boolean
     */
    public function hasConstructorInjection()
    {
        return $this->getAnnotations()->has('Inject');
    }

    /**
     * Get a FIFO queue of classes that need to be instantiated
     */
    public function getInjectionQueue()
    {
        $queue = new \splQueue();
        $constructor = $this->getConstructor();

        if ($constructor) {
            $parameters = $constructor->getParameters();

            foreach ($parameters as $parameter) {
                $class = $parameter->getClass();

                if (!$class) {
                    throw new \RuntimeException('Cannot inject non-object properties into constructors at this time');
                }

                $className = $class->getName();
                //echo "Parameter {$parameter->getName()} is $className<br>";
                $queue->enqueue($className);
            }
        }

        return $queue;
    }
}
