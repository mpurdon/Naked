<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */
namespace Naked\Persistence;

/**
 * Unit of Work pattern implementation
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class UnitOfWork
{
    protected $newObjects = array();
    protected $dirtyObjects = array();
    protected $removedObjects = array();
    protected $inTransaction = false;
    protected $logger;
    protected $cache;

    /**
     * Constructor
     *
     * @Inject
     */
    public function __construct(\Naked\Log $logger, \Naked\Cache $cache)
    {
        $this->logger = $logger;
        $this->cache = $cache;
    }

    /**
     * Register a new Object
     */
    public function registerNew($object)
    {
        // Do not allow changes if we are processing the objects
        if ($this->inTransaction) {
            return false;
        }
        if ($object->id > 0) {
            throw new \RuntimeException('You can not register a new object with a valid Id of "' . print_r($Object->id, true) . '"');
        }
        if (array_key_exists($object->id, $this->dirtyObjects)) {
            throw new \RuntimeException('You can not register a new object if it has already been marked as dirty');
        }
        if (array_key_exists($object->id, $this->removedObjects)) {
            throw new \RuntimeException('You can not register a new object if it has already been marked as removed');
        }
        $this->logger->log("** UoW - Registering " . get_class($object) . " as new");
        $this->newObjects[$object->id] = $object;
    }

    /**
     * Register an existing object as dirty
     */
    public function registerDirty($object)
    {
        // Do not allow changes if we are processing the objects
        if ($this->inTransaction) {
            return false;
        }
        if ($object->id <= 0) {
            throw new \RuntimeException('You can not register a dirty object with an invalid Id of "' . print_r($Object->ID, true) . '"');
        }
        if (array_key_exists($object->id, $this->removedObjects)) {
            throw new \RuntimeException('You can not register a dirty object if it has already been marked as removed');
        }
        if (! array_key_exists($object->id, $this->dirtyObjects) && ! array_key_exists($object->id, $this->newObjects)) {
            $this->logger->log("** UoW - Registering " . get_class($object) . " as dirty");
            $this->dirtyObjects[$object->id] = $object;
        }
        return true;
    }

    /**
     * Register an existing object as removed
     */
    public function registerRemoved($object)
    {
        // Do not allow changes if we are processing the objects
        if ($this->inTransaction) {
            return false;
        }
        if ($object->id <= 0) {
            throw new \RuntimeException('You can not register a removed object with an invalid Id of "' . print_r($Object->ID, true) . '"');
        }
        if (array_key_exists($object->id, $this->newObjects)) {
            unset($this->newObjects[$object->id]);
        }
        if (array_key_exists($object->id, $this->dirtyObjects)) {
            unset($this->dirtyObjects[$object->id]);
        }
        if (! array_key_exists($object->id, $this->removedObjects)) {
            $this->logger->log("** UoW - Registering " . get_class($object) . " as removed");
            $this->removedObjects[$object->id] = $object;
        }
    }

    /**
     * Register an object as clean
     */
    public function registerClean($object)
    {
        // Do not allow changes if we are processing the objects
        if ($this->inTransaction) {
            throw new \RuntimeException('You can not register a clean object while we are processing our workload');
        }
        if ($object->id <= 0) {
            throw new \RuntimeException('You can not register a clean object with an invalid Id of "' . print_r($Object->ID, true) . '"');
        }

        $this->logger->log("** UoW - Registering " . get_class($object) . " as clean");
        $cacheKey = 'object_cache_' . get_class($object) . '_' . $object->id;
        Naked_App::getWorkspace()->getCache()->save(serialize($object), $cacheKey);
    }

    /**
     * Make all of the changes permanent
     */
    public function commit()
    {
        if ($this->hasWorkToDo()) {
            $this->logger->log("** UoW - commiting changes");
            $this->beginTransaction();
            $this->insertNew();
            $this->updateDirty();
            $this->deleteRemoved();
            $this->endTransaction();
        }
    }

    /**
     * Determine if we have anything to process
     *
     * @return boolean
     */
    public function hasWorkToDo()
    {
        return $this->hasNewObjects() || $this->hasDirtyObjects() || $this->hasRemovedObjects();
    }

    /**
     * Determine if we have any new objects to insert
     *
     * @return boolean
     */
    public function hasNewObjects()
    {
        return count($this->newObjects) > 0;
    }

    /**
     * Determine if we have any dirty objects to update
     *
     * @return boolean
     */
    public function hasDirtyObjects()
    {
        return count($this->dirtyObjects) > 0;
    }

    /**
     * Determine if we have any removed objects to delete
     *
     * @return boolean
     */
    public function hasRemovedObjects()
    {
        return count($this->removedObjects) > 0;
    }

    /**
     * Prevent the registering of objects and start a database transaction
     */
    protected function beginTransaction()
    {
        Naked_App::getWorkspace()->getDb()->beginTransaction();
        $this->inTransaction = true;
    }

    /**
     * Allow the registering of objects and commit a database transaction
     */
    protected function endTransaction()
    {
        Naked_App::getWorkspace()->getDb()->commit();
        $this->inTransaction = false;
    }

    /**
     * Roll back any database changes and prevent further processing.
     */
    protected function rollback()
    {
        Naked_App::getWorkspace()->getDb()->rollback();
        $this->inTransaction = false;
    }

    /**
     * Insert new Objects
     */
    private function insertNew()
    {
        if ($this->hasNewObjects() && $this->inTransaction) {
            foreach ($this->newObjects as $object) {
                $objectClass = get_class($object);
                try {
                    Naked_Objects::$objectClass()->insert($object);
                    $cacheKey = 'object_cache_' . $objectClass . '_' . $object->id;
                    Naked_App::getWorkspace()->getCache()->save(serialize($object), $cacheKey);
                    $createSignal = new Naked_UnitOfWork_Signal_Create();
                    $createSignal->sender($this)->context(array('original'=>null, 'current'=>$object));
                    Naked_App::getWorkspace()->getSignals()->acceptSignal($createSignal);
                }
                catch (Exception $e) {
                    Naked_App::getWorkspace()->getLogger()->err("Unit of Work: Could not insert new $objectClass: $object [$e]");
                    $this->rollback();
                    throw new \RuntimeException("Could not add new $objectClass: $object [$e]");
                }
            }
        }
    }

    /**
     * Update changed objects
     */
    private function updateDirty()
    {
        if ($this->hasDirtyObjects() && $this->inTransaction) {
            foreach ($this->dirtyObjects as $object) {
                $objectClass = get_class($object);
                if ($object->isValid()) {
                    try {
                        $oldVersion = Naked_Objects::$objectClass()->get($object->id);
                        Naked_Objects::$objectClass()->update($object);
                        $cacheKey = 'object_cache_' . $objectClass . '_' . $object->id;
                        Naked_App::getWorkspace()->getCache()->save(serialize($object), $cacheKey);
                        $updateSignal = new Naked_UnitOfWork_Signal_Update();
                        $updateSignal->sender($this)->context(array('original'=>$oldVersion, 'current'=>$object));
                        Naked_App::getWorkspace()->getSignals()->acceptSignal($updateSignal);
                    }
                    catch (Exception $e) {
                        Naked_App::getWorkspace()->getLogger()->err("Unit of Work: Could not update existing $objectClass: $object [$e]");
                        $this->rollback();
                        throw new \RuntimeException("Could not update existing $objectClass: $object [$e]");
                    }
                } else {
                    Naked_App::getWorkspace()->getLogger()->err("Unit of Work: $objectClass $object is not valid");
                    throw new Naked_UnitOfWork_ValidationException($object . ' is not valid');
                }
            }
        }
    }

    /**
     * Delete removed objects
     */
    private function deleteRemoved()
    {
        if ($this->hasRemovedObjects() && $this->inTransaction) {
            foreach ($this->removedObjects as $object) {
                $objectClass = get_class($object);
                try {
                    Naked_Objects::$objectClass()->delete($object);
                    $cacheKey = 'object_cache_' . $objectClass . '_' . $object->id;
                    Naked_App::getWorkspace()->getCache()->remove($cacheKey);
                    $removeSignal = new Naked_UnitOfWork_Signal_Remove();
                    $removeSignal->sender($this)->context(array('original'=>$object, 'current'=>null));
                    Naked_App::getWorkspace()->getSignals()->acceptSignal($removeSignal);
                }
                catch (Exception $e) {
                    Naked_App::getWorkspace()->getLogger()->err("Unit of Work: Could not delete $objectClass: $object [$e]");
                    $this->rollback();
                    throw new \RuntimeException("Could not delete $objectClass: $object");
                    return false;
                }
            }
        }
    }
}

