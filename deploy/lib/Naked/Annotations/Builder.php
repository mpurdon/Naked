<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Annotations;

use Naked\Annotations\ReflectionClass;

/**
 * Annotation builder
 *
 * Given a class it can parse the doc blocks inside each method and property to
 * determine if there are any annotations specified.
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Builder
{
    /**
     * Constructor
     * @param unknown_type $cache
     */
    public function __construct()
    {}

    /**
     * Get the annotations for a given class
     *
     * @param string $class
     */
    public function get($class)
    {
        $annotations = new ClassAnnotations();
        $reflectionClass = new ReflectionClass($class);

        $this->getConstructorInjectionAnnotations($annotations, $reflectionClass);
        $this->getSetterInjectionAnnotations($annotations, $reflectionClass);
        $this->getFormFieldPropertyAnnotations($annotations, $reflectionClass);

        return $annotations;
    }

    /**
     * Handle the constructor injection annotations
     *
     * @param Naked\Annotations\ClassAnnotations $annotations
     * @param Naked\Annotations\ReflectionClass $reflectionClass
     */
    protected function getConstructorInjectionAnnotations($annotations, $reflectionClass)
    {
        $constructorAnnotations = $reflectionClass->get('constructor');
        if ($constructorAnnotations) {
            foreach($constructorAnnotations as $annotation) {
                if ($annotation->isA('Inject')) {
                    $annotations->setConstructorInjectionDependencies($reflectionClass->getConstructorInjectionQueue());
                }
            }
        }
    }

    /**
     * Handle the setter injection annotations
     *
     * @param Naked\Annotations\ClassAnnotations $annotations
     * @param Naked\Annotations\ReflectionClass $reflectionClass
     */
    protected function getSetterInjectionAnnotations($annotations, $reflectionClass)
    {
        $allMethodAnnotations = $reflectionClass->get('methods');
        if ($allMethodAnnotations) {
            foreach($allMethodAnnotations as $method => $methodAnnotations) {
                foreach ($methodAnnotations as $annotation) {
                    if ($annotation->isA('Inject')) {
                        $annotations->addSetterInjectionMethod($method, $reflectionClass->getMethodInjectionDependency($method));
                    }
                }
            }
        }
    }

    /**
     * Handle the form field property annotations
     *
     * @param Naked\Annotations\ClassAnnotations $annotations
     * @param Naked\Annotations\ReflectionClass $reflectionClass
     */
    protected function getFormFieldPropertyAnnotations($annotations, $reflectionClass)
    {
        $allPropertyAnnotations = $reflectionClass->get('properties');

        if ($allPropertyAnnotations) {
            foreach($allPropertyAnnotations as $property => $propertyAnnotations) {
                foreach ($propertyAnnotations as $annotation) {
                    if ($annotation->isA('Field')) {
                        $annotations->addFormFieldProperty($property, $annotation);
                    }
                }
            }
        }
    }


}