<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Persistence\Rdbms\MySQLi;

class Result
{
    protected $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function fetch()
    {
        return $this->result->fetch_object();
    }

    public function fetchAll()
    {
        $results = new \ArrayObject();

        while ($dataObject = $this->result->fetch_object()) {
            $results->append($dataObject);
        }

        return $results;

    }

    public function numRows()
    {
        return $this->result->num_rows;
    }

    public function close()
    {
        $this->result->close();
    }
}
