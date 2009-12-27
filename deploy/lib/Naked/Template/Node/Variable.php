<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */
namespace Naked\Template\Node;

use Naked\Template\Exception;

/**
 * A template variable, resolvable against a given context. The variable may be
 * a hard-coded string (if it begins and ends with single or double quote
 * marks)
 */
class Variable
{
    protected $var;
    protected $literal;
    protected $lookups;
    protected $doTranslation = false;
    const ATTRIBUTE_SEPARATOR = '.';

    /**
     * Constructor
     * @param mixed $var
     */
    public function __construct ($var)
    {
        $this->var = $var;
        //echo "Determining variable type: ";
        // Is this an integer or a float?
        if (is_int($var) || is_float($var)) {
            //echo "numeric literal";
            $this->literal = $var;
        } else {
            // Is this a string that needs translating?
            if (strpos('_(', $var) === 0 && strrpos(')') > 0) {
                //echo "translation required";
                $this->doTranslation = true;
                $var = substr($var, 2, - 1);
            }
            // If this is wrapped with quotes this is a literal
            if (strpos($var, '"') === 0) {
                //echo "string literal";
                $this->literal = substr($var, 1, - 1);
            } else {
                //echo "context variable";
                $this->lookups = explode(Variable::ATTRIBUTE_SEPARATOR, $var);
            }
        }
        //echo "<br>";
    }

    /**
     * Resolve this variable using the context
     *
     * @param Naked\Template\Context
     * @return mixed
     */
    public function resolve ($context)
    {
        //echo "Resolving context for variable {$this->var}<br>";
        if ($this->hasLookups()) {
            //echo "Resolving lookups<br>";
            $value = $this->resolveLookup($context);
        } else {
            //echo "Resolving literal<br>";
            $value = $this->literal;
        }
        if ($this->doTranslation) {
            //echo "translating<br>";
            $value = $this->translate($value);
        }
        return $value;
    }

    /**
     * Determine if this variable has lookups
     *
     * @return boolean
     */
    public function hasLookups ()
    {
        return is_array($this->lookups);
    }

    /**
     * Search through the context for the variable we are looking for
     *
     * @param Naked\Template\Context $context
     * @return mixed
     */
    protected function resolveLookup ($context)
    {
        foreach ($this->lookups as $lookup) {
            $current = $context->$lookup;
        }
        return $current;
    }

    /**
     * @todo Actually translate the value
     *
     * @param string $value
     * @return string
     */
    protected function translate ($value)
    {
        return $value;
    }

    /**
     * Get the name of this variable
     *
     * @return string
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * The string representation of this variable
     *
     * @return string
     */
    public function __toString ()
    {
        return $this->getName();
    }
}
