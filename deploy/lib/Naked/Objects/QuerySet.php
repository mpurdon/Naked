<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Objects;

use Naked\DI;

use Naked\Objects\Query\MySQL;

/**
 * Represents the public API for the ORM
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class QuerySet implements \IteratorAggregate
{
    public $query;

    /**
     * @var Naked\Objects\Map
     */
    protected $map;

    /**
     * @var Naked\Persistence
     */
    protected $persistence;

    /**
     * @var ArrayObject
     */
    protected $results;

    /**
     * Constructor
     *
     * @Inject
     */
    public function __construct($map, $persistence)
    {
        // @todo Query should be injected into the QuerySet as well...
        $this->query = new MySQL($map);
        $this->map = $map;
        $this->persistence = $persistence;
    }

    /**
     * Get the number of items this query matches
     */
    public function count()
    {
        return $this->query->getCount();
    }

    /**
     * Perform the query and return a single object matching
     */
    public function get()
    {
        $args = func_get_args();
        $results = call_user_func_array(array($this, 'filter'), $args);
        if (count($results) > 1) {
            throw new \InvalidArgumentException("get() returned more than one object using " . implode(',', $args));
        }

        return $results;
    }

    /**
     * Get the latest object
     */
    public function latest($byField=null)
    {
        $latestBy = is_null($byField) ? 'id' : $byField;
        $latestQuery = clone $this;
        $latestQuery->query->limit(1);
        $latestQuery->query->addOrdering("-$latestBy");

        return $latestQuery->get();
    }

    /**
     * Deletes the records in the current QuerySet
     */
    public function delete()
    {
        $deleteQuery = clone $this;
        $deleteQuery->query->clearOrdering();
        $results = $deleteQuery->getIterator();
        foreach ($results as $result) {
            $this->deleteById($result['id']);
        }
        // @todo clear caching
    }

    /**
     * Delete a record by id
     */
    public function deleteById()
    {
        // @todo Implement deleteById();
    }

    /**
     * Updates all elements in the current QuerySet with the provided values
     */
    public function update()
    {
        $args = func_get_args();

        if (count($args) == 0) {
            throw new \InvalidArgumentException("Updating requires you specify some values to update");
        }

        $updateQuery = clone $this;
        $updateQuery->query->addUpdateValues($args);
        return $updateQuery->execute();
    }

    /**
     * Returns a clone of this QuerySet
     */
    public function all()
    {
        return clone($this);
    }

    /**
     * Return a new QuerySet instance with the args ANDed to the existing set
     */
    public function filter()
    {
        $args = func_get_args();
        $clone = clone $this;
        foreach ($args as $arg) {
            $clone->query->addFilter($arg);
        }

        return $clone;
    }

    /**
     * Return a new QuerySet instance with the args ANDed to the existing set
     */
    public function exclude()
    {
        $args = func_get_args();
        $clone = clone $this;
        $clone->query->addExclusion($args);

        return $clone;
    }

    /**
     * Returns a new QuerySet with the ordering changed
     */
    public function orderBy()
    {
        $args = func_get_args();
        $clone = clone $this;
        $clone->query->clearOrdering();

        foreach ($args as $arg) {
            $clone->query->addOrdering($arg);
        }

        return $clone;
    }

    /**
     * Reverses the ordering of the QuerySet
     */
    public function reverse()
    {
        $clone = clone $this;
        $clone->query->reverseOrdering();

        return $clone;
    }

    /**
     * LImit the number of results returned
     *
     * @param integer $limit
     * @param integer $offset
     */
    public function limit($limit, $offset=0)
    {
        $clone = clone $this;
        $clone->query->limit($limit, $offset);

        return $clone;
    }

    /**
     * Apply the changes provided to all records matched in this queryset
     *
     * @param array $values
     */
    public function change($values)
    {
        echo "Changing values for records in queryset<br>";
    }

    /**
     * The string representation of this QuerySet - essentially the SQL
     */
    public function __toString()
    {
        return (string)$this->query;
    }

    /**
     * Clone this object, breaking the relationship to the current Query object
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }

    /**
     * Used to lazily evaluate this query set
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        if (is_null($this->results)) {
            // @todo How do I know that it's failed?
            $sql = $this->query->getQueryString();
            echo "Executing query: $sql<br>";
            $recordset = $this->persistence->query($sql);
            $this->results = $this->getObjectsFromRecords($recordset->fetchAll());
        }

        return $this->results;
    }

    protected function getObjectsFromRecords($records)
    {
        $objects = array();

        foreach($records as $record) {
            $objects[] = $this->getObjectFromRecord($record);
        }

        return $objects;
    }

    /**
     * Given a query result record, get an object
     *
     * @param stdClass $record
     */
    protected function getObjectFromRecord($record)
    {
        $className = $this->map->getClassName();

        // Instantiate the class with all of the requirements added
        $object = DI::container()->create($className);

        foreach($this->map->getPropertyMaps() as $propertyMap) {
            //echo '<pre>',var_dump($propertyMap),'</pre>';
            $field = new $propertyMap['fieldType']($propertyMap);
            // Assign the field the first time
            $object->$propertyMap['property'] = $field;
            // We can assign again now because the DomainModel knows how to work with Fields
            $object->$propertyMap['property'] = $record->$propertyMap['field'];
            //echo '<pre>',var_dump($propertyMap),'</pre>';
            $object->finishedLoading();
        }

        return $object;
    }
}