<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */
namespace Naked\Annotations;


use Naked\Annotations;

/**
 * Reflection class that knows how to get annotations from the target class
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class ReflectionClass extends \ReflectionClass
{
    /**
     * Annotations for this class's constructor
     *
     * @todo This annotation stuff should be moved to the ReflectionMethod class
     * @var Naked\Annotations
     */
    protected $annotations = array();

    /**
     * Constructor
     *
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct($class);
        $this->searchForAnnotations();
    }

    /**
     * Get the Annotations for this class by type
     */
    public function get($type)
    {
        if (isset($this->annotations[$type])) {
            return $this->annotations[$type];
        }

        return null;
    }

    /**
     * Set up the annotations for this class based ont he constructor doc block
     */
    protected function searchForAnnotations()
    {
        $this->searchForConstructorAnnotations();
        $this->searchForMethodAnnotations();
        $this->searchForPropertyAnnotations();
    }

    protected function searchForConstructorAnnotations()
    {
        $constructor = $this->getConstructor();
        if ($constructor) {
            $comment = $constructor->getDocComment();
            $this->annotations['constructor'] = $this->parseDocBlock($comment);
        }
    }

    /**
     * Look in the doc blocks for the class properties for any annotations
     */
    protected function searchForPropertyAnnotations()
    {
        $properties = $this->getProperties(\ReflectionProperty::IS_PROTECTED);
        foreach ($properties as $property) {
            $comment = $property->getDocComment();
            if (!$comment) {
                continue;
            }
            //echo "Parsing doc block:<pre>$comment</pre>";
            $annotations = $this->parseDocBlock($comment);
            if (count($annotations) > 0) {
                $this->annotations['properties'][$property->getName()] = $annotations;
            }
        }
    }

    /**
     * Look in the doc blocks for the class methods for any annotations
     */
    protected function searchForMethodAnnotations()
    {
        $methods = $this->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($method->isConstructor()) {
                continue;
            }
            $comment = $method->getDocComment();
            $annotations = $this->parseDocBlock($comment);
            if (count($annotations) > 0) {
                $this->annotations['methods'][$method->getName()] = $annotations;
            }
        }
    }

    /**
     * Parse a doc block into annotations
     *
     * @param string $docBlock
     */
    public function parseDocBlock($docBlock)
    {
        $annotations = array();
        //echo "Parsing doc block:<pre>$docBlock</pre>";
        $annotationStrings = $this->getAnnotationStrings($docBlock);
        foreach ($annotationStrings as $annotationType => $annotationValue) {
            if ($this->isAnnotationString($annotationType)) {
                //echo "Creating new $annotationType annotation<br/>";
                $annotations[] = new Annotation($annotationType, $annotationValue);
            }
        }
        return $annotations;
    }

    /**
     * Split a string into blocks based on the @ character
     *
     * @return array
     */
    protected function getAnnotationStrings($docBlock)
    {
        $matches = array();
        $strings = explode("\n", $docBlock);
        if (count($strings) > 0) {
            $regex = '#@(\S+)(.*)#';
            foreach ($strings as $string) {
                $match = false;
                preg_match($regex, $string, $match);
                if ($match) {
                    $matches[$match[1]] = trim($match[2]);
                }
            }
        }
        //echo "Found possible annotations<br>";
        //echo '<pre>',var_dump($matches),'</pre>';
        return $matches;
    }

    /**
     * Determine if the passed in string contains an annotation
     *
     * @return boolean
     */
    protected function isAnnotationString($string)
    {
        //echo "Checking if '$string' is an annotation: ";
        if (stripos($string, 'Inject') === 0) {
            //echo "yes<br/>";
            return true;
        }

        if (stripos($string, 'Field') === 0) {
            //echo "yes<br/>";
            return true;
        }

        //echo "no<br/>";
        return false;
    }

    /**
     * Determine if this class requires constructor injection
     *
     * @return boolean
     */
    public function hasConstructorInjection()
    {
        // @todo This should all be using the annotations class through the DI
        return count($this->annotations['constructor']) > 0;
    }

    /**
     * Get a FIFO queue of classes that need to be instantiated for the constructor
     *
     * @return array
     */
    public function getConstructorInjectionQueue()
    {
        $queue = array();
        $constructor = $this->getConstructor();

        if (!$constructor) {
            return $queue;
        }

        $parameters = $constructor->getParameters();

        foreach ($parameters as $parameter) {
            $class = $parameter->getClass();

            if (!$class) {
                throw new \RuntimeException('Cannot inject non-object properties into constructors at this time');
            }

            $className = $class->getName();
            //echo "Parameter {$parameter->getName()} is $className<br>";
            $queue[] = $className;
        }

        return $queue;
    }

    /**
     * Get a dependency that need to be instantiated for a given method
     *
     * @return string
     */
    public function getMethodInjectionDependency($method)
    {
        $method = $this->getMethod($method);

        if (!$method) {
            return null;
        }

        $parameters = $method->getParameters();

        foreach ($parameters as $parameter) {
            $class = $parameter->getClass();

            if (!$class) {
                throw new \RuntimeException('Cannot inject non-object properties into setters at this time');
            }

            $className = $class->getName();
            //echo "Parameter {$parameter->getName()} is $className<br>";
            return $className;
        }

        return null;
    }
}

