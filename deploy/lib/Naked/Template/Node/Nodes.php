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
 * A List of Nodes
 *
 * @package Naked
 * @subpackage Template
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Nodes
{
    /**
     * @var boolean
     */
    protected $containsNonText = false;
    /**
     * @var array
     */
    protected $list;

    /**
     * Render all of the child nodes into one string
     *
     * @param Naked\Template\Context $context
     */
    public function render($context)
    {
        //echo 'Rendering node list<br>';
        $bits = array();

        foreach($this->list as $node) {
            //echo 'Rendering node: ' . get_class($node) . '<br>';

            if ($node instanceof Node) {
                $bits[] = $node->render($context);
            } else {
                $bits[] = $node;
            }
        }

        return implode('', $bits);
    }

    /**
     * Add a note to the list of nodes
     *
     * @param Naked\Template\Node $node
     */
    public function append($node)
    {
        $this->list[] = $node;
    }
}
