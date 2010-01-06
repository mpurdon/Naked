<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Template\Node;

use Naked\Template\Node\Variable;

/**
 * Filter Expression
 *
 * Parses a variable token and its optional filters (all as a single string),
 * and return a list of tuples of the filter name and arguments.
 */
class FilterExpression
{
    /**
     * @var string
     */
    protected $token;
    /**
     * @var string
     */
    protected $var;
    /**
     * @var array
     */
    protected $filters;
    /**
     * @var string
     */
    protected $regEx;

    const SEPARATOR = '|';
    const ARGUMENT_SEPARATOR = ':';

    /**
     * Constructor
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;

        $matches = array();

        //$regEx = htmlspecialchars($this->getRegEx());
        //echo "Parsing Expression: '{$token}' with filter expression {$regEx}<br>";
        $numMatches = preg_match_all($this->getRegEx(), (string) $token, $matches, PREG_SET_ORDER);

        //echo "Found {$numMatches} matches<br>";
        //var_dump($matches);

        $var = null;
        $filters = array();

        foreach($matches as $match) {
            //var_dump($match);

            if(is_null($var)) {
                // i18n Constant handling
                if (array_key_exists('i18n_constant', $match)) {
                    $i18n_constant = $match['i18n_constant'];
                    if($i18n_constant == '') {
                        $var = '""';
                    } else {
                        // @todo: This should be translated
                        $var = sprintf('"%s"', $i18n_constant);
                    }
                }

                // Regular constant
                if (array_key_exists('constant', $match)) {
                    $var = sprintf('"%s"', str_replace('"', '\"', $match['constant']));
                }

                // Variable
                if (array_key_exists('var', $match)) {
                    $var = $match['var'];
                }

                //echo "Found variable: {$var}<br>";

                if (is_null($var)) {
                    throw new Exception('Could not find variable at start of ' . $token);
                }
            } else {
                // Build filters
                if (array_key_exists('filter_name',$match)) {
                    $filter_name = $match['filter_name'];
                    //echo "Found filter for {$var}: {$filter_name}<br>";

                    $args = array();
                    if (array_key_exists('i18n_arg', $match) && strlen($match['i18n_arg']) > 0) {
                        // @todo This should be translated
                        $args[] = str_replace('\"', '"', $match['i18n_arg']);
                    }

                    if (array_key_exists('constant_arg', $match) && strlen($match['constant_arg']) > 0) {
                        $args[] = str_replace('\"', '"', $match['constant_arg']);
                    }

                    if (array_key_exists('var_arg', $match) && strlen($match['var_arg']) > 0) {
                        $args[] = new Variable($match['var_arg']);
                    }
                    $filters[$filter_name] = $args;
                }
            }
        }

        $this->var = new Variable($var);
        $this->filters = $filters;
    }

    /**
     * Get the regex used to parse a filter expression
     *
     * @return string
     */
    protected function getRegEx()
    {
        if (is_null($this->regEx)) {
            $this->regEx = sprintf('#%2$s"(?P<i18n_constant>%1$s)"%3$s|^"(?P<constant>%1$s)"|^(?P<var>[%4$s]+)|(?:%5$s(?P<filter_name>\w+)(?:%6$s(?:%2$s"(?P<i18n_arg>%1$s)"%3$s|"(?P<constant_arg>%1$s)"|(?P<var_arg>[%4$s]+)))?)#',
                '[^"]*(?:[^"]*)*',
                preg_quote('_('),
                preg_quote(')'),
                '\w.',
                preg_quote(self::SEPARATOR),
                preg_quote(self::ARGUMENT_SEPARATOR)
                );
        }

        return $this->regEx;
    }

    /**
     * Resolves a context
     *
     * @param array $context
     * @param boolean $ignoreContext
     * @return mixed
     */
    public function resolve($context, $ignoreContext=false)
    {
        $contextVar = $this->var->resolve($context);

        // Apply any filters we found
        foreach ($this->filters as $filter => $arguments) {
            $filterMethod = $filter . 'Filter';
            if (method_exists($this, $filterMethod)) {
                $contextVar = $this->$filterMethod($contextVar, $this->filters[$filter]);
            }
        }

        return $contextVar;
    }

    /**
     * The string representation of this Filter Expression
     *
     * @return string
     */
    public function __toString()
    {
        return $this->token;
    }

    // @todo These filters should be moved into a filter directory or something
    // @todo How do end users extends the template filter expressions?

    /**
     * Apply a default value if the context variable is null
     *
     * @param mixed $var
     * @param array $arguments
     * @return mixed
     */
    protected function defaultFilter($var, $arguments)
    {
        if (is_null($var)) {
            return $arguments[0];
        }

        return $var;
    }

    /**
     * Apply a lowering filter to the context variable
     *
     * @param mixed $var
     * @param array $arguments
     * @return string
     */
    protected function lowerFilter($var, $arguments)
    {
        return strtolower($var);
    }

    /**
     * Apply a uppercasing filter to the context variable
     *
     * @param mixed $var
     * @param array $arguments
     * @return string
     */
    protected function upperFilter($var, $arguments)
    {
        return strtoupper($var);
    }

    /**
     * Pluralize depending on the value of the variable
     *
     * @param mixed $var
     * @param array $arguments
     * @return string
     */
    protected function pluralizeFilter($var, $arguments)
    {
        if ($var == 1) {
            return '';
        }

        if (count($arguments) > 0) {
            return $arguments[0];
        }

        return 's';
    }

    /**
     * Depending on a value return the appropriate string
     *
     * @param mixed $var
     * @param array $arguments
     * @return string
     */
    protected function yesnoFilter($var, $arguments)
    {
        if (isset($arguments[0]) && count($arguments[0]) > 0) {
            $options = explode(',', $arguments[0]);
        } else {
            $options = array('yes','no');
        }

        if (is_null($var)) {
            if (count($options) == 3) {
                return $options[2];
            }

            return $options[1];
        }

        $var = (bool) $var;
        if ($var === true) {
            return $options[0];
        }

        return $options[1];
    }
}
