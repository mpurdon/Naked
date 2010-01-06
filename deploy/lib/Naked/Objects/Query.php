<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Objects;

/**
 * Represents the interface a persistence mechanism must implement
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
abstract class Query
{
    protected $map;
    protected $limit = array();
    protected $ordering = array();
    protected $filters = array();
    protected $exclusions = array();
    protected $reverseOrdering = false;

    /**
     * Constructor
     *
     * @param \Naked\Objects\Map $map
     */
    public function __construct(\Naked\Objects\Map $map)
    {
        $this->map = $map;
    }

    /**
     * The string representation of this Query
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->getQueryString();
        // __toString is not allowed to throw exceptions
        } catch (\RuntimeException $e) {
            $query = '';
        }
    }

    /**
     * Get the SQL that this query object represents
     *
     * @return string
     */
    public function getQueryString()
    {
        $query = 'SELECT ' .
                  $this->getColumns() . ' ' .
                  $this->getFromClause() . ' ' .
                  $this->getWhere() . ' ' .
                  $this->getOrdering() . ' ' .
                  $this->getLimit();

        return $query;
    }


    protected function preSqlSetup()
    {

    }

    protected function getColumns()
    {
        return '*';
    }

    protected function getFromClause()
    {
        return "FROM {$this->map->getTableName()} {$this->map->getTableAlias()}";
    }

    protected function getWhere()
    {
        $where = '';
        if (count($this->filters) > 0) {
            $where = 'WHERE ';

            $whereStrings = array();
            foreach ($this->filters as $filter) {
                $whereStrings[] = $this->mapCriteriaToSql($filter);
            }

            $where .= implode(' AND ', $whereStrings);
        }

        return $where;
    }

    protected function getOrdering()
    {
        $ordering = '';
        if (count($this->ordering) > 0) {
            $ordering = 'ORDER BY ';

            $orderStrings = array();
            foreach ($this->ordering as $order) {
                $orderString = $order['property'] . ' ' . ($order['direction'] == '-' ? 'DESC' : 'ASC');
                $orderStrings[] = $orderString;
            }

            $ordering .= implode(', ', $orderStrings);
        }

        return $ordering;
    }

    protected function getLimit()
    {
        switch (count($this->limit)) {
            case 2:
                return 'LIMIT ' . $this->limit[0] . ' OFFSET ' . $this->limit[1];
            case 1:
                return 'LIMIT ' . $this->limit[0];
            default:
                return '';
        }
    }

    public function limit($amount, $offset=0)
    {
        $this->limit = array($amount, $offset);
        echo "Added limit of {$amount} with offset {$offset}<br>";
    }

    public function addOrdering($order)
    {
        $regEx = '#^(?P<direction>\+|\-)? ?(?P<property>\w+)$#';
        $matches = array();
        $foundMatch = preg_match($regEx, $order, $matches);

        if (!$foundMatch) {
            throw new \InvalidArgumentException("The value you specified '$order' is not a valid ordering criteria");
        }

        $this->ordering[] = array(
            'property' => $matches['property'],
            'direction' => $matches['direction']
        );

        echo "Added ordering of {$matches['property']} with direction {$matches['direction']}<br>";
    }

    public function clearOrdering()
    {
        $this->ordering = array();
    }

    public function addFilter($filter)
    {
        $criteria = new Criteria($filter);
        $this->filters[] = $criteria;
        echo "Added filter $criteria<br>";
    }

    public function addExclusion($exclusion)
    {
        $this->exclusions[] = $exclusion;
    }

    public function reverseOrdering()
    {
        $this->reverseOrdering = true;
    }

    /**
     * Used to convert criteria into a SQl where fragment
     *
     * @param Naked\Objects\Criteria $criteria
     * @return string
     */
    abstract protected function mapCriteriaToSql($criteria);
}
