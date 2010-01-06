<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Persistence\Rdbms\MySQLi;

class Statement
{
    protected $statement;

    public function __construct($statement)
    {
        $this->statement = $statement;
    }
}
