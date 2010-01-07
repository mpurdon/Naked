<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked;

use Naked\Objects\Manager;

/**
 * Represents and domain level object in the application
 *
 * @abstract
 * @author Matthew Purdon <matthew@codenaked.org>
 */
abstract class DomainModel
{
    // @todo Add the ability to annotate versionable, auditable, searchable so they can be handled in signals

    /**
     * @Field \Naked\Field\Id
     */
    protected $id;

    /**
     * @var \Naked\Persistence\UnitOfWork
     */
    protected $unitOfWork;

    /**
     * Should the Unit of Work track changes to this object?
     *
     * @var boolean
     */
    protected $trackChanges = false;

    /**
     * Constructor
     *
     * @Inject
     * @param \Naked\UnitOfWork $unitOfWork
     */
    public function __construct(\Naked\Persistence\UnitOfWork $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }

    /**
     * Magic Method to handle getting this object's properties
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $methodName = 'get' . ucfirst($name);

        // Look for a dedicated getter for this property
        if ($this->hasMethod($methodName)) {
            return $this->$methodName();
        }

        // Ensure that this object has the property before getting it
        if ($this->hasProperty($name)) {
            return $this->$name;
        }

        throw new \RuntimeException($name . ' is not a property of ' . get_class($this));
    }

    /**
     * Magic Method to handle getting this object's properties
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        $methodName = 'set' . ucfirst($name);

        // Look for a dedicated setter for this property
        if (in_array($methodName, get_class_methods(get_class($this)))) {
            $this->$methodName($value);
            $this->markDirty();
            return $this;
        }

        // Ensure that this object has the property before setting it
        if (array_key_exists($name, get_object_vars($this))) {
            // Let the UoW know it has to deal with this
            $this->markDirty();

            $this->$name = $value;
            return $value;
        }

        throw new \RuntimeException($name . ' is not a property of ' .  get_class($this));
    }

    /**
     * If the array has the property we are looking for, go ahead and set it.
     *
     * @param string $property
     * @param array $properties
     */
    public function setIfHasProperty($property, $properties)
    {
        if (array_key_exists($property, $properties)) {
            $this->$property = $properties[$property];
        }
    }

    /**
    * Determine if this object has the specified property
    *
    * @param string $property
    * @return boolean
    */
    public function hasProperty($property)
    {
        return property_exists($this, $property);
    }

    /**
    * Determine if this object has the specified method
    *
    * @param string $method
    * @return boolean
    */
    public function hasMethod($method)
    {
        return method_exists($this, $method);
    }

    /**
     * Set the object as being loaded so that any setter changes will
     * automatically register this object as dirty.
     */
    public function finishedLoading()
    {
        $this->trackChanges = true;
    }

    /**
     * Mark this object as being removed
     */
    public function remove()
    {
        $this->markRemoved();
    }

    /**
     * Mark the current object as new in the Unit of Work
     */
    protected function markNew()
    {
        $this->unitOfWork->registerNew($this);
        $this->finishedLoading();
    }

    /**
     * Mark the current object as clean (fresh from the database)
     * in the Unit of Work
     */
    protected function markClean()
    {
        $this->unitOfWork->registerClean($this);
    }

    /**
     * Mark the current object as dirty (changed) in the Unit of Work
     */
    protected function markDirty()
    {
        if (true === $this->trackChanges) {
            $this->unitOfWork->registerDirty($this);
        }
    }

    /**
     * Mark the current object as removed in the Unit of Work
     */
    protected function markRemoved()
    {
        $this->unitOfWork->registerRemoved($this);
    }

    /**
     * Create a new object based on the passed in parameters
     *
     * @param array $properties
     * @param string $context
     */
    abstract public static function create($properties, $context='default');

    /**
     * Get a mapper for this tpe of object
     *
     * You can ovveride this method in child classes to load specialized mappers
     * for the model you are working with.
     *
     * @return Naked\Objects\Mapper
     */
    public static function objects()
    {
        // @todo Definitely create the manager using the DIC - maybe using model as context?
        $map = new \Naked\Objects\Map(get_called_class(), DI::Container()->getAnnotations());
        $persistence = DI::Container()->get('Naked\Persistence\Rdbms\MySQLi');
        //$persistence = new \Naked\Persistence\Rdbms\MySQLi;

        return new Manager($map, $persistence);
    }
}
