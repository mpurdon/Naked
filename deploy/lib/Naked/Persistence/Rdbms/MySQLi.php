<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Persistence\Rdbms;

use Naked\Persistence\Rdbms;
use Naked\Persistence\Rdbms\MySQLi\Statement;
use Naked\Persistence\Rdbms\MySQLi\Result;

class MySQLi extends Rdbms
{
    /**
     * Constructor
     *
     * @Inject
     * @param Naked\Application\Configuration $configuration
     */
    public function __construct(\Naked\Application\Configuration $configuration)
    {
        parent::__construct($configuration);

        $this->instance = mysqli_init();
    }

    public function connect()
    {
        //$backtrace = debug_backtrace();
        //$message = "Call stack {$backtrace[2]['class']}::{$backtrace[2]['function']} => {$backtrace[1]['class']}::{$backtrace[1]['function']}";

        if (!$this->isConnected()) {
            $this->isConnected = $this->instance->real_connect(
                                    $this->host,
                                    $this->username,
                                    $this->password,
                                    $this->db);

            if (mysqli_connect_errno()) {
                throw new \RuntimeException('Could not connect: ' . mysqli_connect_error());
            }
        }
    }

    public function disconnect()
    {
        $this->instance->close();
        $this->connected = false;
        //Zend_Registry::get('logger')->info("Naked_Db: Disconnected from database {$this->db}");
    }

    public function query($sql)
    {
        $this->connect();

        //Zend_Registry::get('logger')->info("Naked_Db: Running SQL query:\n{$sql}");

        $result = $this->instance->query($sql);

        if (!$result) {
            throw new \RuntimeException('Could not execute SQL query ' . $this->instance->error);
        }

        return new Result($result);
    }

    /**
     * Quote and escape the provided value
     *
     * @param mixed $value
     * @return string
     */
    public function quote($value)
    {
        $this->connect();
        $value = $this->instance->escape_string($value);

        return "'{$value}'";
    }

    /**
     * Escape the provided value
     *
     * @param mixed $value
     * @return string
     */
    public function escape($value)
    {
        $this->connect();
        return $this->instance->escape_string($value);
    }


    public function prepare($sql)
    {
        $this->connect();

        $statement = $this->instance->prepare($sql);

        if($statement) {
            return new Statement($statement);
        }

        return false;
    }

    public function beginTransaction()
    {
        $this->connect();
        // Turn off autocommit.
        $this->instance->autocommit(false);
        $this->inTransaction = true;
    }

    public function commit()
    {
        if (!$this->inTransaction) {
            throw new \RuntimeException('Attempted to commit when no transaction started');
        }

        $this->instance->commit();
        $this->inTransaction = false;
    }

    public function rollback()
    {
        if (!$this->inTransaction) {
            throw new \RuntimeException('Attempted to commit when no transaction started');
        }

        $this->instance->rollback();
        $this->inTransaction = false;
    }
}
