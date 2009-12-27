<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Template\Node;

use Naked\Template\Node;

/**
 * Template Text Node
 *
 * @package Naked
 * @subpackage Template
 */
class TextNode extends Node
{
    /**
     * Render this node with the given context
     *
     * @param Naked\Template\Context $context
     * @return string
     */
    public function render($context)
    {
        return $this->value;
    }
}
