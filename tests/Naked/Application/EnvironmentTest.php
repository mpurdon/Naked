<?php
require_once 'deploy\lib\Naked\Application\Environment.php';
require_once 'PHPUnit\Framework\TestCase.php';
/**
 * Environment test case.
 */
class EnvironmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Environment
     */
    private $Environment;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        // TODO Auto-generated EnvironmentTest::setUp()
        $this->Environment = new Environment(/* parameters */);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated EnvironmentTest::tearDown()
        $this->Environment = null;
        parent::tearDown();
    }
    /**
     * Constructs the test case.
     */
    public function __construct ()
    {    // TODO Auto-generated constructor
    }
    /**
     * Tests Environment->__construct()
     */
    public function test__construct ()
    {
        // TODO Auto-generated EnvironmentTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");
        $this->Environment->__construct(/* parameters */);
    }
    /**
     * Tests Environment->getRoutes()
     */
    public function testGetRoutes ()
    {
        // TODO Auto-generated EnvironmentTest->testGetRoutes()
        $this->markTestIncomplete("getRoutes test not implemented");
        $this->Environment->getRoutes(/* parameters */);
    }
}

