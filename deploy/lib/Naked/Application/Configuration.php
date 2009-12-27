<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Application;

/**
 * The configuration for this application
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Configuration
{
    /**
     * Allow changes to this configuration
     *
     * @var boolean
     */
    protected $readOnly = false;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Constructor
     *
     * @author Matthew Purdon <matthew@codenaked.org>
     */
    public function __construct()
    {}

    /**
     * Prevent further changes from happening for this configuration
     */
    public function setReadOnly()
    {
        $this->readOnly = true;
    }

    /**
     * Set the value of a configuration option
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        if ($this->readOnly) {
            throw new RuntimeException('Trying to set a value on a read-only configuration');
        }

        $this->options[$key] = $value;
    }

    /**
     * Set up the options using an array of options
     *
     * @param array $options
     */
    public function init($options)
    {
        if ($this->readOnly) {
            throw new RuntimeException('Trying to set a value on a read-only configuration');
        }

        $this->options = $options;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        //echo "getting name $name<br>";
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        //echo "getting name $name<br>";
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return null;
    }
}