<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */
namespace Naked\Objects\Query;

use Naked\Objects\Query;

/**
 * Builds queries from criterion and mapping for a MySQL database
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class MySQL extends Query
{
    /**
     * Maps a criteria object to a MySQL where clause fragment
     *
     * @param Naked\Objects\Criteria $criteria
     * @return string
     */
    protected function mapCriteriaToSql($criteria)
    {
        $field = $this->map->getFieldFromProperty($criteria->property);

        if (!$field) {
            throw new \RuntimeException('Could not find a field for the property ' . $criteria->property);
        }

        $field = $this->map->getTableAlias() . '.' . $field;

        //echo "Getting {$criteria->comparitor} clause for $field<br>";

        switch ($criteria->comparitor) {
            case 'eq':
                // same as exact but less characters
            case 'exact':
                return "$field = '{$criteria->value}'";
            case 'iexact':
                return "$field ILIKE '{$criteria->value}'";
            case 'contains':
                return "$field LIKE '%{$criteria->value}%'";
            case 'icontains':
                return "$field ILIKE '%{$criteria->value}%'";
            case 'in':
                return "$field IN ({$criteria->value})";
            case 'gt':
                return "$field > '{$criteria->value}'";
            case 'gte':
                return "$field >= '{$criteria->value}'";
            case 'lt':
                return "$field < '{$criteria->value}'";
            case 'lte':
                return "$field <= '{$criteria->value}'";
            case 'startswith':
                return "$field LIKE '{$criteria->value}%'";
            case 'istartswith':
                return "$field ILIKE '{$criteria->value}%'";
            case 'endswith':
                return "$field LIKE '%{$criteria->value}'";
            case 'iendswith':
                return "$field ILIKE '%{$criteria->value}'";
            case 'range':
                $values = explode(',', $criteria->value);
                if (count($values) != 2) {
                    throw new \RuntimeException('You must specify exactly two values for a range criteria');
                }
                return "$field BETWEEN '{$values[0]}' AND '{$values[1]}'";
            case 'year':
                return "extract ('year' FROM $field) = '{$criteria->value}'";
            case 'month':
                return "extract ('month' FROM $field) = '{$criteria->value}'";
            case 'day':
                return "extract ('day' FROM $field) = '{$criteria->value}'";
            case 'dow':
                return "extract ('dow' FROM $field) = '{$criteria->value}'";
            case 'isnull':
                if ($criteria->value == 0) {
                    return "$field IS NOT NULL";
                } else {
                    return "$field IS NULL";
                }
            case 'search':
                return "MATCH (tablename, $field) AGAINST ({$criteria->value} IN BOOLEAN MODE)";
            case 'regex':
            case 'iregex':
                throw new \RuntimeException('Regular expression searching is not yet supported');
            default:
                throw new \RuntimeException('The comparison operator you supplied "' . $criteria->comparitor . '" is not supported');
        }
    }

    /**
     * Executes this query
     */
    protected function execute()
    {

    }
}