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
 * A basic car
 */
abstract class Car extends DomainModel
{
    /**
     * @var string
     * @Field \Naked\Field\Char maxLength=15
     */
    protected $model;

    /**
     * @var Year
     * @Field \Naked\Field\PositiveInteger
     */
    protected $year;

    /**
     * @var Driver
     * @Field \Naked\Field\ForeignKey class=\Naked\Car\Driver
     */
    protected $driver;

    /**
     * Inject the driver
     *
     * We have to use setter injection here because we are already using the
     * constructor of our parent DomainModel to inject the UnitOfWork that all
     * Domain Objects use. Makes me wonder if I should bother with constructor
     * injection at all?
     *
     * @Inject
     * @param Driver $driver
    public function setDriver(Driver $driver)
    {
        $this->driver = $driver;
        //echo "I just set my driver to {$driver->name}<br>";
    }
     */

    /**
     * The string representation of this object
     *
     * @return string
     */
    public function __toString() {
        return basename(get_class($this)) . ' ' . $this->model . ' driven by ' . $this->driver->name;
    }

    /**
     * Create a Car object instance
     *
     * @param array $properties
     */
    public static function create($properties, $context='default')
    {
        $car = DI::container()->get(__CLASS__, $context);
        if ($car) {
            $car->setIfHasProperty('model', $properties);
        }
        $car->finishedLoading();

        return $car;
    }
}
