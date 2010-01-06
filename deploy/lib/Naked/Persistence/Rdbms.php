<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Persistence;

use Naked\Persistence;

/**
 *
 * @abstract
 */
abstract class Rdbms implements Persistence
{
    protected $instance;

    protected $host;
    protected $username;
    protected $password;
    protected $db;

    protected $isConnected = false;

    /**
     * Constructor
     *
     * @Inject
     * @param Naked\Application\Configuration $configuration
     */
    public function __construct(\Naked\Application\Configuration $configuration)
    {
        $this->host = $configuration->db_default_host;
        $this->username = $configuration->db_default_user;
        $this->password = $configuration->db_default_pass;
        $this->db = $configuration->db_default_name;
    }

    public function isConnected()
    {
        return $this->isConnected;
    }

    public function quote($value)
    {
        return addslashes($value);
    }

    // @todo Should this be a Naked\Objects\Query?
    abstract public function beginTransaction();
    abstract public function commit();
    abstract public function rollback();
}
