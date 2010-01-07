<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Objects;

use Naked\Util;

use Naked\Annotations;

use Naked\Util\Text;
use Naked\Field;


/**
 * Maps Domain Objects to fields that can be persisted
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Map
{
    protected $className;
    protected $superType;
    protected $tableName;
    protected $propertyMaps;
    protected $relationshipMaps;
    protected $identityMap;
    protected $fieldList;

    /**
     * Constructor
     *
     * @param string $class
     */
    public function __construct($class, $annotations)
    {
        $this->className = $class;
        $this->determineSuperType();
        $this->determineTableName();
        $this->propertyMaps = array();
        $this->relationshipMaps = array();
        $this->identityMap = null;
        $this->fieldList = array();

        $this->addPropertiesFromAnnotations($annotations);
    }

    /**
     * Get the name of the class this map is for
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Get the name of the table this map is for
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Get the alias of the table name this map is for
     *
     * @return string
     */
    public function getTableAlias()
    {
        return $this->tableAlias;
    }

    /**
     * Look for property annotations and use them to create property objects
     *
     * @param unknown_type $annotations
     */
    public function addPropertiesFromAnnotations(\Naked\Annotations $annotations)
    {
        $classAnnotations = $annotations->forClass($this->className);

        if ($classAnnotations->hasFormFieldProperties()) {
            foreach ($classAnnotations->getFormFieldProperties() as $property => $field) {
                //echo "Property $property: <pre>",var_dump($field),'</pre>';
                $propertyField = $this->getFieldFromAnnotation($field);
                $this->addProperty($property, $propertyField);
            }
        }

        //echo 'Annotations: <pre>',var_dump($annotations),'</pre>';
    }

    /**
     * Figure out what the supertype of this class is so we can do single table inheritance.
     */
    protected function determineSuperType()
    {
        // @todo this mapping needs to be cached to get away from reflection
        $superType = null;
        //echo "Finding supertype for {$this->className}<br>";
        $reflectedClass = new \ReflectionClass($this->className);

        //$indent = '';

        while ($reflectedClass = $reflectedClass->getParentClass()) {
            //echo "$indent {$reflectedClass->getName()}<br>";
            //$indent .= '_';
            if ($reflectedClass->getName() == 'Naked\DomainModel') {
                break;
            }

            $superType = $reflectedClass->getName();
        }

        //echo "Super type was $superType<br>";
        $this->superType = $superType;
    }

    /**
     * Determine if this Map is representing an object that has a parent
     *
     * @return boolean
     */
    public function hasInheritance()
    {
        return !is_null($this->superType);
    }

    /**
     * Based on the supertype and class name, figure out the table for this mapping
     */
    protected function determineTableName()
    {
        if ($this->hasInheritance()) {
            $this->tableName = $this->normalize($this->superType);
        } else {
            $this->tableName = $this->normalize($this->className);
        }

        $this->tableAlias = $this->createTableAlias($this->tableName);
    }

    /**
     * Given a property annotation, get the proper field object
     *
     * @param Naked\Annotations\Annotation $annotation
     * @return Naked\Field
     */
    protected function getFieldFromAnnotation(\Naked\Annotations\Annotation $annotation)
    {
        $parameters = $annotation->getParameters();
        // The first parameter is the field type
        $FieldType = key($parameters);
        // We don't need the field type once we know it
        array_shift($parameters);

        try {
            $field = new $FieldType($parameters);
            return $field;
        } catch (\Exception $e) {
            throw new \RuntimeException("Could not create a Field of type $FieldType. {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Add an Object Property Map
     *
     * @param string $propertyName
     * @param Naked_Field_Abstract $property
     */
    public function addProperty($propertyName, $property)
    {
        if (! $property instanceof \Naked\Field) {
            //echo $propertyName,' is not a field ',var_dump($property),'<br>';
            return;
        }

        $propertyMap = array('property'=>$propertyName, 'field'=>$this->normalize($propertyName));
        $propertyMap = array_merge($propertyMap, $property->getSpecification());
        $this->fieldList[] = $propertyMap['field'];
        if ($property instanceof \Naked\Field\Id) {
            //echo 'Adding Identity for ',$this->className,'<br>';
            $this->identityMap = $propertyMap;
        }

        //echo 'Adding property ',$propertyName,' for ',$this->className,'<br>';
        $this->propertyMaps[$propertyName] = $propertyMap;
        return $this;
    }

    /**
     * Get the mapping of property to field for the provided property
     *
     * @param $property
     * @return string
     */
    public function getFieldFromProperty($property)
    {
        if (array_key_exists($property, $this->propertyMaps)) {
            return $this->propertyMaps[$property]['field'];
        }

        return false;
    }

    /**
     * Returns a list of properties
     *
     * @param  boolean $withIdentity Should the identity field be included?
     * @return array
     */
    public function getPropertyList($withIdentity = true)
    {
        $propertyList = array();
        foreach ($this->propertyMaps as $map) {
            // Add the identity property if we want it
            if ($map == $this->identityMap && $withIdentity) {
                $propertyList[] = $map['property'];
                continue;
            }
            $propertyList[] = $map['property'];
        }
        return $propertyList;
    }

    /**
     * Returns the list of properties as a string
     *
     * @param boolean $withIdentity Should the identity field be included?
     * @return string
     */
    public function getPropertyListString($withIdentity = true)
    {
        $propertyList = $this->getPropertyList($withIdentity);
        return implode(',', $propertyList);
    }

    /**
     * Return the list of fields that are mapped to the class
     *
     * @return string
     */
    public function getFieldList($withIdentity = true)
    {
        $fieldList = array();
        foreach ($this->propertyMaps as $map) {
            // Add the identity field if we want it
            if ($map == $this->identityMap) {
                if (true == $withIdentity) {
                    $fieldList[] = $map['field'];
                }
            } else {
                $fieldList[] = $map['field'];
            }
        }
        return $fieldList;
    }

    /**
     * Return the list of fields that are mapped to the class
     *
     * @return string
     */
    public function getFieldListString($withIdentity = true)
    {
        $fieldList = $this->getFieldList($withIdentity);
        return implode(',', $fieldList);
    }

    /**
     * Return the identity field that is mapped to the class
     *
     * @return string
     */
    public function getIdentityField()
    {
        if (is_null($this->identityMap)) {
            throw new \RuntimException('There is no identity field specified for the ' . $this->className . ' to ' . $this->tableName . ' Data Map');
        }

        return $this->identityMap['field'];
    }

    /**
     * Return the identity property that is mapped to the table
     *
     * @return string
     */
    public function getIdentityProperty()
    {
        if (is_null($this->identityMap)) {
            throw new \RuntimeException('There is no identity field specified for the ' . $this->className . ' to ' . $this->tableName . ' Data Map');
        }

        return $this->identityMap['property'];
    }

    /**
     * Using the Identity Map information, build a standard where clause.
     *
     * @param string $from
     * @return string
     */
    public function getIdentityClause($from)
    {
        if ($from instanceof $this->className) {
            $propertyName = $this->identityMap['property'];
            $where = $this->identityMap['field'] . ' = ' . $from->$propertyName;

            return $where;
        }

        if (is_int($from) && $from > 0) {
            return $this->identityMap['field'] . ' = ' . $from;
        }

        throw new \RuntimeException('You must provide a  ' . $this->className . ' object to produce an identity where clause.');
    }

    /**
     * Get the list of Property Maps
     *
     * @return array
     */
    public function getPropertyMaps()
    {
        return $this->propertyMaps;
    }

    /**
     * Add a Relationship to another class (composite in nature)
     *
     * @param string $propertyName
     * @param string $className
     * @param string $fieldName - Property in child class that points to parent
     */
    public function addRelationshipMap($propertyName, $className, $fieldName)
    {
        $PropertyMap = array('property'=>$propertyName, 'class'=>$className, 'fk'=>$fieldName);
        $this->relationshipMaps[] = $PropertyMap;

        return $this;
    }

    /**
     * Return a single relationship map for a specified class
     *
     * @param string $class
     * @return array
     */
    public function getRelationshipMap($class)
    {
        foreach ($this->relationshipMaps as $relationship) {
            if ($relationship['class'] == $class) {
                return $relationship;
            }
        }

        return false;
    }

    /**
     * Return the list of relationship maps
     *
     * @return array
     */
    public function getRelationshipMaps()
    {
        return $this->relationshipMaps;
    }

    /**
     * Create an alias for a table name
     *
     * @param string $string
     * @return string
     */
    public function createTableAlias($string)
    {
        $alias = '';
        $parts = explode('_', $string);
        $lastPart = array_pop($parts);
        foreach ($parts as $part) {
            $alias .= $part[0];
        }

        $alias .= '_' . $lastPart;

        return $alias;
    }

    /**
     * Used to convert camel-cased into underscored strings.
     *
     * @param string $string
     * @return string
     */
    protected function normalize($string)
    {
        return Text::backslashedToUnderscores($string);
    }
}
