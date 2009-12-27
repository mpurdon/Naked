<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Template;

/**
 * Lexer
 *
 * @package Naked
 * @subpackage Template
 * @author Matthew Purdon
 */
class Lexer
{
    const BLOCK_TAG_START = '{%';
    const BLOCK_TAG_END = '%}';
    const VARIABLE_TAG_START = '{{';
    const VARIABLE_TAG_END = '}}';
    const COMMENT_TAG_START = '{#';
    const COMMENT_TAG_END = '#}';

    protected $templateString;

    /**
     * Construct the Lexer
     */
    public function __construct($templateString)
    {
        $this->templateString = $templateString;
    }

    /**
     * Get the regex used to tokenize strings
     */
    public function getRegEx()
    {
        $regEx = sprintf('!(%s.*?%s|%s.*?%s|%s.*?%s)!',
            preg_quote(self::BLOCK_TAG_START),
            preg_quote(self::BLOCK_TAG_END),
            preg_quote(self::VARIABLE_TAG_START),
            preg_quote(self::VARIABLE_TAG_END),
            preg_quote(self::COMMENT_TAG_START),
            preg_quote(self::COMMENT_TAG_END));

        return $regEx;
    }

    /**
     * Create tokens from the raw template string
     *
     * @return array
     */
    public function tokenize()
    {
        $inTag = false;

        $tokens = array();
        $regEx = $this->getRegEx();
        //echo "Splitting string '{$this->templateString}' using regex: $regEx<br>";
        $chunks = preg_split($regEx, $this->templateString, 0, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($chunks as $tokenString)  {
            $tokens[] = $this->createToken($tokenString, $inTag);
            $inTag = !$inTag;
        }

        return $tokens;
    }

    /**
     * Create a token from a string
     *
     * @param string $tokenString
     * @param boolean $inTag
     * @return Naked\Token
     * @author Matthew Purdon
     */
    protected function createToken($tokenString, $inTag)
    {
        //echo "Creating a token with '$tokenString' :";

        // If we are not in a token, then this is just plain text
        if (!$inTag) {
            //echo "Text token<br>";
            return new Token(Token::TYPE_TEXT, $tokenString);
        }

        // We found a variable
        if (strpos($tokenString, self::VARIABLE_TAG_START) === 0) {
            //echo "Variable token<br>";
            $startTagLength = strlen(self::VARIABLE_TAG_START);
            $endTagLength = strlen(self::VARIABLE_TAG_END);
            $endPosition = strlen($tokenString) - $startTagLength - $endTagLength;
            $tokenValue = substr($tokenString, $startTagLength, $endPosition);
            return new Token(Token::TYPE_VAR, $tokenValue);
        }

        // We found a code block
        if (strpos($tokenString, self::BLOCK_TAG_START) === 0) {
            //echo "Block token<br>";
            $startTagLength = strlen(self::BLOCK_TAG_START);
            $endTagLength = strlen(self::BLOCK_TAG_END);
            $endPosition = strlen($tokenString) - $startTagLength - $endTagLength;
            $tokenValue = substr($tokenString, $startTagLength, $endPosition);
            return new Token(Token::TYPE_BLOCK, $tokenValue);
        }

        // We found a comment
        if (strpos($tokenString, self::COMMENT_TAG_START) === 0) {
            //echo "Comment token<br>";
            $startTagLength = strlen(self::COMMENT_TAG_START);
            $endTagLength = strlen(self::COMMENT_TAG_END);
            $endPosition = strlen($tokenString) - $startTagLength - $endTagLength;
            $tokenValue = substr($tokenString, $startTagLength, $endPosition);
            return new Token(Token::TYPE_COMMENT, $tokenValue);
        }
    }
}

/**
 * A Token from a template string
 *
 * @package Naked
 * @subpackage Template
 */
class Token
{
    const TYPE_TEXT = 0;
    const TYPE_VAR = 1;
    const TYPE_BLOCK = 2;
    const TYPE_COMMENT = 3;

    /**
     * @var integer
     */
    public $type;
    /**
     * @var string
     */
    public $contents;

    /**
     * Constructor
     *
     * @param integer $type
     * @param string $contents
     */
    public function __construct($type, $contents)
    {
        $this->type = $type;

        if ($type !== Token::TYPE_TEXT) {
            $contents = trim($contents);
        }

        $this->contents = $contents;
    }

    /**
     * Split the contents in a smart way
     */
    public function splitContents()
    {
        $bits = Text::smartSplit($this->contents);
    }

    /**
     * The string representation of this token
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->contents;
    }
}
