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
 * Template Variable Node
 *
 * @package Naked
 * @subpackage Template
 */
class VariableNode extends Node
{
    /**
     * Render this node using the given context
     *
     * @param Naked\Template\Context $context
     * @return mixed
     */
    public function render($context)
    {
        return $this->value->resolve($context);
    }
}
