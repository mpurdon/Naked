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
    protected $annotations;

    /**
     * Constructor
     *
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct($class);
        $this->setAnnotations();
    }

    /**
     * Get the Annotations for this class
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * Set up the annotations for this class based ont he constructor doc block
     */
    protected function setAnnotations()
    {
        $this->annotations = new Annotations();

        $constructor = $this->getConstructor();

        if ($constructor) {
            $comment = $constructor->getDocComment();
            $this->annotations->parseDocBlock($comment);
        }
    }
}
