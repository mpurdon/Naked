<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Annotations;

/**
 * Reflection method that knows how to get annotations from the target method
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class ReflectionMethod extends \ReflectionMethod
{
    /**
     * Annotations for this method
     *
     * @var Naked\Annotations
     */
    protected $annotations;

    /**
     * Constructor
     *
     */
    public function __construct($class, $method)
    {
        parent::__construct($class, $method);
        $this->setAnnotations();
    }

    /**
     * Get the Annotations for this method
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * Set up the annotations for this class based on the method doc block
     */
    protected function setAnnotations()
    {
        $this->annotations = new Annotations();
        $comment = $constructor->getDocComment();
        $this->annotations->parseDocBlock($comment);
    }
}