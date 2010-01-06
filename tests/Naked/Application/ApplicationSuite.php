<?php
require_once 'PHPUnit\Framework\TestSuite.php';
require_once 'tests\Naked\Application\DispatcherTest.php';
require_once 'tests\Naked\Application\EnvironmentTest.php';

/**
 * Static test suite.
 */
class ApplicationSuite extends PHPUnit_Framework_TestSuite
{

    /**
     * Constructs the test suite handler.
     */
    public function __construct()
    {
        $this->setName('ApplicationSuite');
        $this->addTestSuite('EnvironmentTest');
        $this->addTestSuite('DispatcherTest');
    }

    /**
     * Creates the suite.
     */
    public static function suite()
    {
        return new self();
    }
}

