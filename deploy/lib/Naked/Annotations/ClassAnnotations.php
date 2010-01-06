<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Annotations;

/**
 * Represents all of the annotations in a given class
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class ClassAnnotations
{
    protected $constructorInjections = array();
    protected $setterInjections = array();
    protected $formFieldProperties = array();

    public function __construct()
    {}

    public function hasConstructorInjection()
    {
        return count($this->constructorInjections) > 0;
    }

    public function setConstructorInjectionDependencies($dependencies)
    {
        $this->constructorInjections = $dependencies;
        return $this;
    }

    /**
     * Get the constructor injection dependencies for this class
     *
     * @return SplQueue
     */
    public function getConstructorInjectionDependencies()
    {
        return $this->constructorInjections;
    }

    public function hasSetterInjection()
    {
        return count($this->setterInjections) > 0;
    }

    public function getSetterInjectionDependencies()
    {
        return $this->setterInjections;
    }

    public function addSetterInjectionMethod($method, $dependency)
    {
        $this->setterInjections[$method] = $dependency;
    }

    public function hasFormFieldProperties()
    {
        return count($this->formFieldProperties) > 0;
    }

    public function getFormFieldProperties()
    {
        return $this->formFieldProperties;
    }

    public function addFormFieldProperty($property, $formField)
    {
        $this->formFieldProperties[$property] = $formField;
    }
}