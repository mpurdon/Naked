<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Template;

use Naked\Template\Node\Nodes,
    Naked\Template\Node\TextNode,
    Naked\Template\Node\FilterExpression,
    Naked\Template\Node\VariableNode;

/**
 * Parser to parse template tokens
 *
 * @package Naked
 * @subpackage Template
 */
class Parser
{
    /**#@+
     * @var array
     */
    protected $tokens;
    protected $tags = array();
    protected $filters = array();
    /**#@-*/

    /**
     * Constructor
     *
     * @param array $tokens
     */
    public function __construct($tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Parse tokens into Nodes
     *
     * @param array
     * @return Naked\Template\Nodes
     */
    public function parse($parseUntil=null)
    {
        if (is_null($parseUntil)) {
            $parseUntil = array();
        }

        $nodeList = new Nodes();

        foreach($this->tokens as $token) {
            // Build a node based on the token type
            switch ($token->type) {
                case Token::TYPE_TEXT:
                    $nodeList->append(new TextNode($token->contents));
                    break;
                case Token::TYPE_VAR:
                    $filterExpression = new FilterExpression($token->contents);
                    $nodeList->append(new VariableNode($filterExpression));
                    break;
            }
        }

        return $nodeList;
    }

    /**
     * Given a filter string, compile it into some PHP
     *
     * @param string $filterString
     */
    protected function compileFilter($filterString)
    {}
}
