<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked;

/**
 * Represents a model field
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
abstract class Field
{
    public $dataType;
    public $choices;
    public $default = '';
    public $blank = false;
    public $readOnly = false;

    protected $value;
    protected $validationErrors = array();

    /**
     * Constructor
     *
     * @param array $parameters
     * @return Naked_Field_Abstract
     */
    public function __construct($parameters=array())
    {
        // Process any parameters that we were passed
        $properties = array('choices', 'default', 'blank', 'readOnly');

        foreach ($parameters as $property => $value) {
            if (in_array($property, $properties)) {
                $this->$property = $value;
            }
        }
    }

    public function getSpecification()
    {
        return array(
                'fieldType' => get_called_class(),
                'dataType' => $this->dataType,
                'choices' => $this->choices,
                'default' => $this->default,
                'blank' => $this->blank,
                'readOnly' => $this->readOnly
                );
    }

    /**
     * Get the value of this field
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of this field
     *
     * @param mixed $value
     */
    public function setvalue($value)
    {
        $this->value = $value;
    }

    /**
    * Determine if this field is valid
    */
    public function isValid()
    {
        // Check if we allow this field to be blank
        if (!$this->blank && strlen($this->value) == 0) {
            $this->validationErrors[] = 'Value must be specified';
            return false;
        }

        // Check if we have specified a default value
        if (strlen($this->value) == 0) {
            $this->value = $this->default;
        }

        return true;
    }

    /**
     * The string value of this field
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
}
