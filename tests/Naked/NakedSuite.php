<?php
require_once 'PHPUnit\Framework\TestSuite.php';
require_once 'tests\Naked\Application\ApplicationSuite.php';

/**
 * Static test suite.
 */
class NakedSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Constructs the test suite handler.
     */
    public function __construct ()
    {
        $this->setName('NakedSuite');
        $this->addTestSuite('ApplicationSuite');
    }

    /**
     * Creates the suite.
     */
    public static function suite ()
    {
        return new self();
    }
}

