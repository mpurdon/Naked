<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked;

use Naked\Annotations\Annotation;

/**
 * A class or method annotation
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Annotations extends \ArrayObject
{
    /**
     * Constructor
     *
     * @param string $docBlock
     */
    public function __construct($docBlock=null)
    {
        if (!is_null($docBlock)) {
            $this->parseDocBlock($docBlock);
        }
    }

    /**
     * Parse a doc block into annotations
     *
     * @param string $docBlock
     */
    public function parseDocBlock($docBlock)
    {
        //echo "Parsing doc block:<pre>$docBlock</pre>";
        $annotationStrings = $this->getAnnotationStrings($docBlock);

        foreach($annotationStrings as $annotationType => $annotationValue) {
            if ($this->isAnnotationString($annotationType)) {
                //echo "Creating new $annotationType annotation<br/>";
                $annotation = new Annotation($annotationType, $annotationValue);
                $this->append($annotation);
            }
        }
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

        //echo "no<br/>";
        return false;
    }

    /**
     * Determine if the desired annotation exists in the children
     *
     * @param string $annotation
     * @return boolean
     */
    public function has($type)
    {
        foreach($this->getIterator() as $annotation) {
            if (strcasecmp($annotation->getType(),$type) == 0) {
                return true;
            }
        }

        return false;
    }
}
