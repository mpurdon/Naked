<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace index\models;

use Naked\DomainModel;

/**
 * A driver specification
 */
abstract class Driver extends DomainModel
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Create a Driver object instance
     *
     * @param array $properties
     */
    public static function create($properties, $context='default')
    {
        $driver = DI::container()->get(__CLASS__, $context);
        if ($driver) {
            $driver->setIfHasProperty('name', $properties);
        }
        $driver->finishedLoading();

        return $driver;
    }
}
