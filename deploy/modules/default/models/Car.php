<?php

namespace models;

/**
 * A basic car
 */
abstract class Car {
    public $model;

    /**
     * @Inject
     * @param Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function __toString() {
        return basename(get_class($this)) . ' ' . $this->model . ' driven by ' . $this->driver->getName();
    }
}
