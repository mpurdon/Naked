<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked;

use Naked\Util\Text,
    Naked\Template\Lexer,
    Naked\Template\Parser;

/**
 * Template
 *
 * @package Naked
 * @author Matthew Purdon
 */
class Template
{
    /**
     * @var string
     */
    protected $templateString;

    /**
     * @var array
     */
    protected $nodes;

    /**
     * Constructor
     */
    public function __construct($templateString)
    {
        $this->templateString = $templateString;
        $this->compile($templateString);
    }

    /**
     * Compile the template into a node set
     */
    public function compile($templateString)
    {
        $lexer = new Lexer($templateString);
        $parser = new Parser($lexer->tokenize());
        $this->nodes = $parser->parse();
    }

    /**
     * Render the node set
     */
    public function render($context)
    {
        return $this->nodes->render($context);
    }

    /**
     * Return the string representation of this template
     */
    public function __toString()
    {
        return $this->render();
    }
}
